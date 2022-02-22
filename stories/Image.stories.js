import { storiesOf } from "@storybook/vue3";
import Placeholder from "./assets/placeholder.png";

storiesOf("Styleguide", module).add("Image", () => ({
  template: `
      <div class="main">
          <p>Images can be added through the WYSIWYG fields in the CMS using the \`Insert Media\` action. The layout of
              the image can be modified with the \`Alignment / style\` dropdown field.</p>
          <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam egestas, diam sit amet faucibus dictum,
              turpis libero porttitor diam, id pellentesque orci augue adipiscing risus. Nulla id lacus eros. Nam
              porttitor semper enim, sit amet vestibulum arcu adipiscing sed. Proin dignissim, ligula id rhoncus
              fringilla, nunc lectus luctus magna, vitae cursus neque arcu id risus. Phasellus eu risus quis mauris
              consectetur luctus. Sed facilisis diam mauris, a vulputate ligula. Sed nec purus mauris. Nulla euismod
              massa ac nisl gravida dictum. Fusce leo turpis, accumsan non ultricies sed, consectetur id dui.
              Suspendisse porttitor odio in ipsum dignissim mollis.</p>
          <img class="leftAlone" title="" src="${Placeholder}" alt="" width="400" height="200">
          <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam egestas, diam sit amet faucibus dictum,
              turpis libero porttitor diam, id pellentesque orci augue adipiscing risus. Nulla id lacus eros. Nam
              porttitor semper enim, sit amet vestibulum arcu adipiscing sed. Proin dignissim, ligula id rhoncus
              fringilla, nunc lectus luctus magna, vitae cursus neque arcu id risus. Phasellus eu risus quis mauris
              consectetur luctus. Sed facilisis diam mauris, a vulputate ligula. Sed nec purus mauris. Nulla euismod
              massa ac nisl gravida dictum. Fusce leo turpis, accumsan non ultricies sed, consectetur id dui.
              Suspendisse porttitor odio in ipsum dignissim mollis.</p>
          <img class="center" title="" src="${Placeholder}" alt="" width="400" height="200">
          <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam egestas, diam sit amet faucibus dictum,
              turpis libero porttitor diam, id pellentesque orci augue adipiscing risus. Nulla id lacus eros. Nam
              porttitor semper enim, sit amet vestibulum arcu adipiscing sed. Proin dignissim, ligula id rhoncus
              fringilla, nunc lectus luctus magna, vitae cursus neque arcu id risus. Phasellus eu risus quis mauris
              consectetur luctus. Sed facilisis diam mauris, a vulputate ligula. Sed nec purus mauris. Nulla euismod
              massa ac nisl gravida dictum. Fusce leo turpis, accumsan non ultricies sed, consectetur id dui.
              Suspendisse porttitor odio in ipsum dignissim mollis.</p>
          <img class="left" title="" src="${Placeholder}" alt="" width="400" height="200">
          <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam egestas, diam sit amet faucibus dictum,
              turpis libero porttitor diam, id pellentesque orci augue adipiscing risus. Nulla id lacus eros. Nam
              porttitor semper enim, sit amet vestibulum arcu adipiscing sed. Proin dignissim, ligula id rhoncus
              fringilla, nunc lectus luctus magna, vitae cursus neque arcu id risus. Phasellus eu risus quis mauris
              consectetur luctus. Sed facilisis diam mauris, a vulputate ligula. Sed nec purus mauris. Nulla euismod
              massa ac nisl gravida dictum. Fusce leo turpis, accumsan non ultricies sed, consectetur id dui.
              Suspendisse porttitor odio in ipsum dignissim mollis.</p>
          <img class="right" title="" src="${Placeholder}" alt="" width="400" height="200">
          <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam egestas, diam sit amet faucibus dictum,
              turpis libero porttitor diam, id pellentesque orci augue adipiscing risus. Nulla id lacus eros. Nam
              porttitor semper enim, sit amet vestibulum arcu adipiscing sed. Proin dignissim, ligula id rhoncus
              fringilla, nunc lectus luctus magna, vitae cursus neque arcu id risus. Phasellus eu risus quis mauris
              consectetur luctus. Sed facilisis diam mauris, a vulputate ligula. Sed nec purus mauris. Nulla euismod
              massa ac nisl gravida dictum. Fusce leo turpis, accumsan non ultricies sed, consectetur id dui.
              Suspendisse porttitor odio in ipsum dignissim mollis.</p>
          <h3>Images (Caption)</h3>
          <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam egestas, diam sit amet faucibus dictum,
              turpis libero porttitor diam, id pellentesque orci augue adipiscing risus. Nulla id lacus eros. Nam
              porttitor semper enim, sit amet vestibulum arcu adipiscing sed. Proin dignissim, ligula id rhoncus
              fringilla, nunc lectus luctus magna, vitae cursus neque arcu id risus. Phasellus eu risus quis mauris
              consectetur luctus. Sed facilisis diam mauris, a vulputate ligula. Sed nec purus mauris. Nulla euismod
              massa ac nisl gravida dictum. Fusce leo turpis, accumsan non ultricies sed, consectetur id dui.
              Suspendisse porttitor odio in ipsum dignissim mollis.</p>
          <div class="captionImage leftAlone" style="width: 400px;">
            <img class="leftAlone" title="" src="${Placeholder}" alt="" width="400" height="200">
            <p class="caption leftAlone">This is a caption</p>
          </div>
          <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam egestas, diam sit amet faucibus dictum,
              turpis libero porttitor diam, id pellentesque orci augue adipiscing risus. Nulla id lacus eros. Nam
              porttitor semper enim, sit amet vestibulum arcu adipiscing sed. Proin dignissim, ligula id rhoncus
              fringilla, nunc lectus luctus magna, vitae cursus neque arcu id risus. Phasellus eu risus quis mauris
              consectetur luctus. Sed facilisis diam mauris, a vulputate ligula. Sed nec purus mauris. Nulla euismod
              massa ac nisl gravida dictum. Fusce leo turpis, accumsan non ultricies sed, consectetur id dui.
              Suspendisse porttitor odio in ipsum dignissim mollis.</p>
          <div class="captionImage center" style="width: 400px;">
            <img class="center" title="" src="${Placeholder}" alt="" width="400" height="200">
            <p class="caption center">This is a caption</p>
          </div>
          <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam egestas, diam sit amet faucibus dictum,
              turpis libero porttitor diam, id pellentesque orci augue adipiscing risus. Nulla id lacus eros. Nam
              porttitor semper enim, sit amet vestibulum arcu adipiscing sed. Proin dignissim, ligula id rhoncus
              fringilla, nunc lectus luctus magna, vitae cursus neque arcu id risus. Phasellus eu risus quis mauris
              consectetur luctus. Sed facilisis diam mauris, a vulputate ligula. Sed nec purus mauris. Nulla euismod
              massa ac nisl gravida dictum. Fusce leo turpis, accumsan non ultricies sed, consectetur id dui.
              Suspendisse porttitor odio in ipsum dignissim mollis.</p>
          <div class="captionImage left" style="width: 400px;">
            <img class="left" title="" src="${Placeholder}" alt="" width="400" height="200">
            <p class="caption left">This is a caption</p>
          </div>
          <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam egestas, diam sit amet faucibus dictum,
              turpis libero porttitor diam, id pellentesque orci augue adipiscing risus. Nulla id lacus eros. Nam
              porttitor semper enim, sit amet vestibulum arcu adipiscing sed. Proin dignissim, ligula id rhoncus
              fringilla, nunc lectus luctus magna, vitae cursus neque arcu id risus. Phasellus eu risus quis mauris
              consectetur luctus. Sed facilisis diam mauris, a vulputate ligula. Sed nec purus mauris. Nulla euismod
              massa ac nisl gravida dictum. Fusce leo turpis, accumsan non ultricies sed, consectetur id dui.
              Suspendisse porttitor odio in ipsum dignissim mollis.</p>
          <div class="captionImage right" style="width: 400px;">
            <img class="right" title="" src="${Placeholder}" alt="" width="400" height="200">
            <p class="caption right">This is a caption</p>
          </div>
          <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam egestas, diam sit amet faucibus dictum,
              turpis libero porttitor diam, id pellentesque orci augue adipiscing risus. Nulla id lacus eros. Nam
              porttitor semper enim, sit amet vestibulum arcu adipiscing sed. Proin dignissim, ligula id rhoncus
              fringilla, nunc lectus luctus magna, vitae cursus neque arcu id risus. Phasellus eu risus quis mauris
              consectetur luctus. Sed facilisis diam mauris, a vulputate ligula. Sed nec purus mauris. Nulla euismod
              massa ac nisl gravida dictum. Fusce leo turpis, accumsan non ultricies sed, consectetur id dui.
              Suspendisse porttitor odio in ipsum dignissim mollis.</p>
      </div>
    `,
}));
