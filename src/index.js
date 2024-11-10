// import apiFetch from "@wordpress/api-fetch";
import { useBlockBindingsUtils } from "@wordpress/block-editor";
import { registerBlockBindingsSource } from "@wordpress/blocks";
import { store as coreStore } from "@wordpress/core-data";
import { useSelect } from "@wordpress/data";

const { apiResponse } = gravatarData;

registerBlockBindingsSource({
  name: "santosguillamot/gravatar",
  getValues({ bindings, select, context }) {
    const { getEditedEntityRecord } = select(coreStore);
    const newValues = {};

    for (const [attributeName, source] of Object.entries(bindings)) {
      const { key, field } = source.args;
      const { gravatar_id: id } =
        getEditedEntityRecord("postType", context?.postType, context?.postId)
          .meta || {};
      // Read from JSON file.
      newValues[attributeName] =
        apiResponse[id || "santosguillamot"][key || field];

      // Fetch from API.
      //   apiFetch({
      //     url: "https://api.gravatar.com/v3/profiles/" + id,
      //     headers: {
      //       Authorization: "Bearer 1234",
      //     },
      //   });
    }
    return newValues;
  },
  setValues({ bindings, select, context }) {
    const { getEditedEntityRecord } = select(coreStore);
    Object.values(bindings).forEach(({ args, newValue }) => {
      const { key, field } = args;
      const { gravatar_id: id } =
        getEditedEntityRecord("postType", context?.postType, context?.postId)
          .meta || {};
      apiResponse[id || "santosguillamot"][key || field] = newValue;
    });
    // Update JSON file.
    // DON'T DO THIS. This is just an example.
    wp.ajax.post("update_gravatar_json_data", {
      api_response: JSON.stringify(apiResponse),
    });
  },
  canUserEditValue: () => true,
  render({ context, attribute, binding }) {
    const { FieldsList } = useBlockBindingsUtils();
    const id = useSelect((select) => {
      const { getEditedEntityRecord } = select(coreStore);
      return getEditedEntityRecord(
        "postType",
        context?.postType,
        context?.postId
      ).meta?.gravatar_id;
    }, []);
    /**
     * Return object with the following format.
     * ```js
     * {
     *     field_1_key: {
     *         label: 'Field 1 Label',
     *         value: 'Field 1 Value',
     *     },
     *     field_2_key: {
     *         label: 'Field 2 Label',
     *         value: 'Field 2 Value',
     *     },
     *     ...
     * }
     * ```
     */
    const fields = Object.entries(apiResponse[id || "santosguillamot"]).reduce(
      (acc, [key, value]) => {
        acc[key] = {
          label: key
            .replace(/_/g, " ")
            .replace(/\b\w/g, (char) => char.toUpperCase()),
          value: value,
          type: typeof value,
        };
        return acc;
      },
      {}
    );
    return (
      <FieldsList
        fields={fields}
        source={"santosguillamot/gravatar"}
        attribute={attribute}
        currentBinding={binding}
      />
    );
  },
});
