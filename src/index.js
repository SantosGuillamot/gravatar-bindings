// import apiFetch from "@wordpress/api-fetch";
import { registerBlockBindingsSource } from "@wordpress/blocks";

const { apiResponse } = gravatarData;

registerBlockBindingsSource({
  name: "santosguillamot/gravatar",
  getValues({ bindings }) {
    const newValues = {};

    for (const [attributeName, source] of Object.entries(bindings)) {
      const { id, field } = source.args;
      // Read from JSON file.
      newValues[attributeName] = apiResponse[id][field];

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
  setValues({ bindings }) {
    Object.values(bindings).forEach(({ args, newValue }) => {
      const { id, field } = args;
      apiResponse[id][field] = newValue;
    });
    // Update JSON file.
    // DON'T DO THIS. This is just an example.
    wp.ajax.post("update_gravatar_json_data", {
      api_response: JSON.stringify(apiResponse),
    });
  },
  canUserEditValue: () => true,
});
