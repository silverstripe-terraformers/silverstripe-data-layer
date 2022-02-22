import { storiesOf } from "@storybook/vue3";

storiesOf("Styleguide", module).add("Table", () => ({
  template: `
      <div class="main">
        <table>
          <thead>
            <tr>
              <td>#</td>
              <td>First Name</td>
              <td>Last Name</td>
              <td>Username</td>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>1</td>
              <td>Mark</td>
              <td>Otto</td>
              <td>@motto</td>
            </tr>
            <tr>
              <td>2</td>
              <td>Sarah</td>
              <td>Connor</td>
              <td>@sconnor</td>
            </tr>
            <tr>
              <td>3</td>
              <td>Larry</td>
              <td>the Bird</td>
              <td>@larrythebird</td>
            </tr>
          </tbody>
        </table>
      </div>
    `,
}));
