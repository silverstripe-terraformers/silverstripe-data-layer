import { mount } from "@vue/test-utils";
import FlyoutPanel from "./FlyoutPanel.vue";

const createCmp = (propsData) => mount(FlyoutPanel, { propsData });

describe("FlyoutPanel", () => {
  let cmp;

  beforeEach(() => {
    cmp = createCmp({});
  });

  it("is a Vue instance", () => {
    expect(cmp.vm).toBeTruthy();
  });

  describe("Properties", () => {
    it("returns the default value of `open` property", () => {
      expect(cmp.props("open")).toBe(false);
    });

    it("returns the default value of `slideFrom` property", () => {
      expect(cmp.props("slideFrom")).toBe("right");
    });

    describe("Validation", () => {
      const slideFromProp = createCmp({ slideFrom: "right" }).vm.$options.props
        .slideFrom;
      const openProp = createCmp({ open: true }).vm.$options.props.open;

      it("validates `slideFrom` is of type string", () => {
        expect(slideFromProp.type).toBe(String);
      });

      it("validates `open` is of type boolean", () => {
        expect(openProp.type).toBe(Boolean);
      });

      it("validates the value of `slideFrom` property is to be within array ['right', 'left']", () => {
        expect(
          slideFromProp.validator && slideFromProp.validator("invalid-value")
        ).toBeFalsy();
        expect(
          slideFromProp.validator && slideFromProp.validator("right")
        ).toBeTruthy();
        expect(
          slideFromProp.validator && slideFromProp.validator("left")
        ).toBeTruthy();
      });
    });
  });
});
