# Elastic search
This is a helpful guide for starting off a project which is going to use Elastic App search and you want to use Vue (and not the react focused [search-ui](https://github.com/elastic/search-ui) library from Elastic)

## Prep
- Alright so we need to do some prep, you're going to need to have created your index and pushed documents to it. Use [🔍 Silverstripe Search-as-a-Service
](https://github.com/silverstripe/silverstripe-search-service)
- Set up a proxy for your search, we'll use [SilverStripe Elastic Proxy
](https://github.com/madmatt/silverstripe-elastic-proxy)
  - If the search is private facing, use the controller so only authed requests will get through
  - If the search is public facing use the `search-proxy.php` method so your search is faster

## Let's go
- Install your dependencies: `yarn add @elastic/search-ui @elastic/search-ui-app-search-connector`
- Create your search config:
```js
// js/search/searchConfig.js
import AppSearchAPIConnector from "@elastic/search-ui-app-search-connector";

// These are the legit values as we hide them with the proxy
const connector = new AppSearchAPIConnector({
    searchKey: "search-key",
    engineName: "engine-name",
    endpointBase: "/_search",
    cacheResponses: false,
});

const config = {
    apiConnector: connector,
    initialState: {
        resultsPerPage: 15,
    },
};

export default config;
```

### Search component
Let's start with the bones of the component (`js/search/SearchSection.vue`):
```vue
<script setup>
import { ref, computed, reactive } from "vue";
import { SearchDriver } from "@elastic/search-ui";
import config from "./searchConfig";

// We'll create our driver instance with our config from earlier
const driver = new SearchDriver(config);
const { searchTerm, resultsPerPage } = driver.getState();

// We'll set the input value to be tied to the search term pulled from the state (e.g. if a url query param was set)
const searchInputValue = ref(searchTerm);

// We'll set the searchState up and subscribe it to changes from the driver
const searchState = reactive({});
driver.subscribeToStateChanges((state) => {
    searchState.value = state;
});

// When a user submits a search we'll pass the value associated with our `searchInputValue` from earlier
const handleFormSubmit = () =>
    driver.getActions().setSearchTerm(searchInputValue.value);
    
const setCurrentPage = (page) => driver.setCurrent(page);

// These value all change based on state and therefore are computed rather than static
const thereAreResults = computed(
    () =>
        searchState.value?.results &&
        searchState.value?.totalResults &&
        searchState.value.totalResults > 0
);

const results = computed(() => searchState.value?.results ?? []);

const current = computed(() => searchState.value?.current ?? 1);
</script>
```

#### Header component
We can now create the header (`js/search/SearchHeader.vue`):
```vue
<template>
    <form @submit.prevent="$emit('submit', $event.target.value)">
        <input
            type="search"
            inputmode="search"
            autocomplete="on"
            placeholder="Search..."
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
        />
        <button type="submit">Search</button>
    </form>
</template>

<script setup>
const props = defineProps({
    modelValue: {
        required: true,
        type: String,
    },
});
</script>
```

#### Paging info component
We can now create the paging info component (e.g. results 1 - 3 of 10) (`js/search/SearchPagingInfo.vue`):
```vue
template>
    <div class="c-search__paging-info">
        Showing
        {{ start }} -
        {{ Math.min(end, searchState.value?.totalResults) }}
        out of {{ searchState.value?.totalResults }} for "{{
            searchState.value?.searchTerm
        }}"
    </div>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
    searchState: {
        required: true,
        type: Object,
    },
});

// What the staring value is
const start = computed(() => {
    if (!props.searchState.value) {
        return 0;
    }

    return props.searchState.value.totalResults === 0
        ? 0
        : (props.searchState.value.current - 1) *
              props.searchState.value.resultsPerPage +
              1;
});

const end = computed(() => {
    if (!props.searchState.value) {
        return 0;
    }

    return props.searchState.value.totalResults <=
        props.searchState.value.resultsPerPage
        ? props.searchState.value.totalResults
        : start.value + props.searchState.value.resultsPerPage - 1;
});
</script>
```

#### Results component
Let's now create the results list (`js/search/SearchResults.vue`):
```vue
<template>
    <ul>
        <li
            v-for="result in results"
            :key="result.id.raw"
        >
            <SearchResult :result="result" />
        </li>
    </ul>
</template>

<script setup>
import SearchResult from "./SearchResult";

const props = defineProps({
    results: {
        type: Array,
        required: true,
    },
});
</script>
```

#### Result component:
Let's now create the result (`js/search/SearchResult.vue`):
```vue
<template>
    <article>
        <h3>
            <a :href="result.link.raw">{{
                result.title.raw
            }}</a>
        </h3>
        <p v-if="summary" v-html="summary"></p>
    </article>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
    result: {
        type: Object,
        required: true,
    },
});

const summary = computed(() => props.result?.content?.snippet ?? false);
</script>
```

#### Create the monolith pagination component
You can use a third party pagniation library or yolo invent your own like so:
```vue
<template>
    <nav aria-label="Page navigation">
        <ul>
            <li v-show="current > 1">
                <button @click="prev">
                    <!-- SVG GOES HERE -->
                    <span class="u-visually-hidden">Previous</span>
                </button>
            </li>

            <li v-for="i in totalPages">
                <!-- ELLIPSIS -->
                <span v-if="showFrontEllipses(i)">...</span>

                <!-- CURRENT -->
                <button
                    v-if="showCurrent(i)"
                    @click="clickHandler(i)"
                    :title="`Viewing page ${i} of ${totalPages}`"
                >
                    {{ i }}
                </button>

                <!-- LOOK AHEAD/BEHIND -->
                <button
                    v-if="showPrevNext(i)"
                    @click="clickHandler(i)"
                    :title="`View page ${i} of ${totalPages}`"
                >
                    {{ i }}
                </button>

                <!-- ELLIPSIS -->
                <span v-if="showEndEllipses(i)">...</span>
            </li>

            <li v-show="current < totalPages">
                <button @click="next">
                    <!-- SVG GOES HERE -->
                    <span class="u-visually-hidden">Next</span>
                </button>
            </li>
        </ul>
    </nav>
</template>

<script setup>
const props = defineProps({
    clickHandler: {
        type: Function,
        required: true,
    },
    totalPages: {
        type: Number,
        required: true,
    },
    current: {
        type: Number,
        required: true,
    },
});

const next = () =>
    props.clickHandler(Math.min(props.current + 1, props.totalPages));
    
const prev = () => props.clickHandler(Math.max(props.current - 1, 1));

// Conditional logic around what to show to users based on the current page
const showFrontEllipses = (i) => i === props.current - 2 && i !== 1;
const showCurrent = (i) => i === props.current;
const showPrevNext = (i) =>
    (i !== props.current &&
        (i === props.current + 1 ||
            i === props.current - 1 ||
            i === props.totalPages ||
            i === 1)) ||
    (props.current === 1 && i === props.current + 2) ||
    (props.current === props.totalPages && i === props.current - 2);
const showEndEllipses = (i) =>
    i === props.current + 2 && i !== props.totalPages;
</script>
```

#### Update search component

Now we can put a template in the search component (`js/search/SearchSection.vue`):
```vue
<template>
    <SearchHeader v-model="searchInputValue" @submit="handleFormSubmit" />
    <SearchPagingInfo :search-state="searchState" />
    <SearchResults v-show="thereAreResults" :results="results" />
    <SearchPagination
        v-show="thereAreResults"
        :total-pages="Math.min(searchState?.value?.totalPages ?? 0, 100)"
        :click-handler="setCurrentPage"
        :current="current"
    />
</template>
```

## Fin
You should now be able to see a search working and behaving as expected, no issues (aside from looking ugly, its your time to shine with styles 💅)



