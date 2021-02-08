import app from "../app.js";
import requester from "../requester.js";

const $selUnitId = $("#unitId");
const limit = 10;

const initSelect2 = () => {
  $selUnitId.select2({
    width: "100%",
    placeholder: "Caja / Cajas",
    minimumResultsForSearch: limit,
    minimumInputLength: 0,
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
          results: result.data.map(unit => {
            return {
              id: unit.unitId,
              text: `${unit.unitSingularName} / ${unit.unitPluralName}`
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
          config.append("column", "unitSingularName,unitPluralName");
          config.append("order", "asc,asc");
          config.append("needle", params.data.needle);
          config.append("limit", limit);
          config.append("offset", (params.data.page - 1) * limit);
          const fetched = await requester.submitData("unit/list/active-units", config);
          setdata(fetched.result);
        } catch (err) {
          console.log(err);
          app.renderAlert({
            autohide: false,
            container: "alert",
            message: typeof err == "string" ? err : "Intentalo de nuevo, si el error persiste contacta al administrador",
            type: "danger"
          });

          setdata({total: 0, filtered: 0, data: []});
        }
      }
    }
  });

  $selUnitId.on("select2:select", () => app.rebuildTooltips());
  app.rebuildTooltips();
};

const destroySelect2 = () => {
  $selUnitId.html('');
  $selUnitId.select2("destroy");
};

const enableClearOnCloseSelect2 = () => {
  $selUnitId.on("select2:close", () => initSelect2());
};

const setSelectedOption = (id, text) => {
  $selUnitId.html('').append(new Option(text, id, true, true)).trigger("change");
  app.rebuildTooltips();
};

export {initSelect2, destroySelect2, enableClearOnCloseSelect2, setSelectedOption};