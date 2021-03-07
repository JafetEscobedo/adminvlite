import app from "../app.js";
import requester from "../requester.js";

const $selUserRoleId = $("#userRoleId");
const limit = 10;

const initSelect2 = () => {
  $selUserRoleId.select2({
    width: "100%",
    placeholder: "Administrador",
    minimumResultsForSearch: limit,
    minimumInputLength: 1,
    language: "es",
    ajax: {
      delay: 250,
      method: "post",
      data: params => {
        return {
          needle: params.term || '',
          page: params.page || 1
        };
      },
      processResults: (result, params) => {
        params.page = params.page || 1;
        return {
          results: result.data.map(userRole => {
            return {
              id: userRole.userRoleId,
              text: userRole.userRoleName
            };
          }),
          pagination: {
            more: (params.page * limit) < result.filtered
          }
        };
      },
      transport: async function (params, setdata) {
        try {
          const config = new FormData();
          config.append("column", "userRoleName");
          config.append("order", "asc");
          config.append("needle", params.data.needle);
          config.append("limit", limit);
          config.append("offset", (params.data.page - 1) * limit);
          const fetched = await requester.submitData("user-role/list/active-user-roles", config);
          setdata(fetched.result);
        } catch (err) {
          console.log(err);
          app.toast({
            className: "danger",
            icon: "fa-times-circle",
            body: typeof err == "string" ? err : "Intentalo de nuevo, si el error persiste contacta al administrador"
          });

          setdata({total: 0, filtered: 0, data: []});
        }
      }
    }
  });

  $selUserRoleId.on("select2:select", () => app.rebuildTooltips());
  app.rebuildTooltips();
};

const destroySelect2 = () => {
  $selUserRoleId.html('');
  $selUserRoleId.select2("destroy");
};

const enableClearOnCloseSelect2 = () => {
  $selUserRoleId.on("select2:close", () => initSelect2());
};

const setSelectedOption = (id, text) => {
  $selUserRoleId.html('').append(new Option(text, id, true, true)).trigger("change");
  app.rebuildTooltips();
};

export {initSelect2, destroySelect2, enableClearOnCloseSelect2, setSelectedOption};