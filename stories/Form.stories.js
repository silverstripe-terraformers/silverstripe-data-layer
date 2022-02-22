import { storiesOf } from "@storybook/vue3";

storiesOf("Styleguide", module).add("Form", () => ({
  template: `
      <div class="main">
         <form action="" method="post" enctype="application/x-www-form-urlencoded">
          <fieldset>
            <div id="Form_TestForm_SimpleText_Holder" class="field text">
              <label class="left" for="Form_TestForm_SimpleText">Simple Text Field</label>
              <div class="middleColumn">
                <input type="text" name="SimpleText" class="text form-control" id="Form_TestForm_SimpleText" aria-required="true">
              </div>
            </div>

            <div id="Form_TestForm_SimpleTextBad_Holder" class="field text holder-bad error">
              <label class="left" for="Form_TestForm_SimpleTextBad">Simple Text Field (bad)</label>
              <div class="middleColumn">
                <input type="text" name="SimpleTextBad" class="text form-control holder-bad" id="Form_TestForm_SimpleTextBad">
              </div>

              <p class="message error">This is an error message</p>
            </div>

            <div id="Form_TestForm_Dropdown_Holder" class="field dropdown">
              <label class="left" for="Form_TestForm_Dropdown">Normal dropdown</label>
              <div class="middleColumn">
                <select name="Dropdown" class="dropdown form-control" id="Form_TestForm_Dropdown" aria-required="true">
                  <option value="1">One option</option>
                  <option value="2">Two option</option>
                </select>
              </div>
            </div>

            <div id="Form_TestForm_Checkbox_Holder" class="field checkbox">
              <input type="checkbox" name="Checkbox" value="1" class="checkbox" id="Form_TestForm_Checkbox" aria-required="true">
              <label class="right" for="Form_TestForm_Checkbox">Checkbox</label>
            </div>

            <div id="CheckboxSet" class="field optionset checkboxset">
              <label class="left">Checkbox set</label>
              <div class="middleColumn">
                <ul class="optionset checkboxset" id="Form_TestForm_CheckboxSet">
                  <li class="odd val1 checkbox">
                    <input id="Form_TestForm_CheckboxSet_1" class="checkbox" name="CheckboxSet[1]" type="checkbox" value="1">
                    <label for="Form_TestForm_CheckboxSet_1">One option</label>
                  </li>

                  <li class="even val2 checkbox">
                    <input id="Form_TestForm_CheckboxSet_2" class="checkbox" name="CheckboxSet[2]" type="checkbox" value="2">
                    <label for="Form_TestForm_CheckboxSet_2">Two option</label>
                  </li>

                  <li class="odd val3 checkbox">
                    <input id="Form_TestForm_CheckboxSet_3" class="checkbox" name="CheckboxSet[3]" type="checkbox" value="3">
                    <label for="Form_TestForm_CheckboxSet_3">Three option</label>
                  </li>
                </ul>
              </div>
            </div>

            <div id="Option" class="field optionset">
              <label class="left">Option</label>
              <div class="middleColumn">
                <ul class="optionset" id="Form_TestForm_Option">
                  <li class="odd val1 radio">
                    <input id="Form_TestForm_Option_1" class="radio" name="Option" type="radio" value="1">
                    <label for="Form_TestForm_Option_1">One option</label>
                  </li>
                </ul>
              </div>
            </div>

            <div id="OptionSet" class="field optionset">
              <label class="left">Option set</label>
              <div class="middleColumn">
                <ul class="optionset" id="Form_TestForm_OptionSet">
                  <li class="odd val1 radio">
                    <input id="Form_TestForm_OptionSet_1" class="radio" name="OptionSet" type="radio" value="1">
                    <label for="Form_TestForm_OptionSet_1">One option</label>
                  </li>

                  <li class="even val2 radio">
                    <input id="Form_TestForm_OptionSet_2" class="radio" name="OptionSet" type="radio" value="2">
                    <label for="Form_TestForm_OptionSet_2">Two option</label>
                  </li>

                  <li class="odd val3 radio">
                    <input id="Form_TestForm_OptionSet_3" class="radio" name="OptionSet" type="radio" value="3">
                    <label for="Form_TestForm_OptionSet_3">Three option</label>
                  </li>
                </ul>
              </div>
            </div>

            <input type="hidden" name="SecurityID" value="183226317cb0c3471c977f9b998e108a35c035ca" class="hidden" id="Form_TestForm_SecurityID">
          </fieldset>

          <div class="Actions">
            <input type="submit" name="action_doForm" value="Submit" class="action btn btn-primary" id="Form_TestForm_action_doForm">
          </div>
        </form>
      </div>
    `,
}));
