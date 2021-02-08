/* global moment, _ */

import app from "../_shared/app.js";
import requester from "../_shared/requester.js";

const $selLength = $("#unitsListLength");
const txtSearch = document.getElementById("unitsListSearch");
const dtUnitsList = $("#unitsList").DataTable({
  language: app.dataTableLang,
  responsive: true,
  serverSide: true,
  processing: true,
  fixedHeader: true,
  pageLength: 10,
  dom: "tipr",
  order: [[0, "desc"]],
  columnDefs: [{
      targets: "_all",
      data: null
    }, {
      targets: [6],
      orderable: false
    }],
  ajax: async (table, setdata) => {
    try {
      const config = app.paginationConfig(table);
      const fetched = await requester.submitData("unit/list/units", config);

      setdata({
        recordsTotal: fetched.result.total,
        recordsFiltered: fetched.result.filtered,
        data: fetched.result.data
      });
    } catch (err) {
      console.log(err);

      app.renderAlert({
        autohide: false,
        container: "alert",
        message: typeof err == "string" ? err : "Intentalo de nuevo, si el error persiste contacta al administrador",
        type: "danger"
      });

      setdata({
        recordsTotal: 0,
        recordsFiltered: 0,
        data: []
      });
    }
  },
  columns: [{
      name: "unitSingularName",
      render: data => _.capitalize(data.unitSingularName)
    }, {
      name: "unitPluralName",
      render: data => _.capitalize(data.unitPluralName)
    }, {
      name: "unitActive",
      render: data => data.unitActive == 'y'
         ? `<span class="badge badge-success">Activa</span>`
         : `<span class="badge badge-danger">Inactiva</span>`
    }, {
      name: "unitCreatedAt",
      render: data => `
        <span title="${moment(data.unitCreatedAt).format(app.dateFormat)}">
          ${moment(data.unitCreatedAt).fromNow()}
        </span>`
    }, {
      name: "unitUpdatedAt",
      render: data => `
        <span title="${moment(data.unitUpdatedAt).format(app.dateFormat)}">
          ${moment(data.unitUpdatedAt).fromNow()}
        </span>`
    }, {
      name: "unitInactivatedAt",
      render: data => !data.unitInactivatedAt ? `<small class="text-muted"><i>No aplica</i></small>` : `
        <span title="${moment(data.unitInactivatedAt).format(app.dateFormat)}">
          ${moment(data.unitInactivatedAt).fromNow()}
        </span>`
    }, {
      render: data => `
        <a title="Actualizar" class="btn btn-xs btn-default" href="${app.url("unit/view/units-list/update/" + data.unitId)}")">
          <i class="fas fa-fw fa-pencil-alt text-info"></i>
        </a>`
    }]
});

const debouncedSearch = _.debounce(needle => dtUnitsList.search(needle).draw(), 250);

$selLength.select2({width: "100%", minimumResultsForSearch: 10});
$selLength.on("select2:select", e => {
  dtUnitsList.page.len(e.target.value).draw();
  app.rebuildTooltips();
});

dtUnitsList.on("responsive-display", () => app.rebuildTooltips());
dtUnitsList.on("draw", () => app.rebuildTooltips());
txtSearch.onkeyup = e => debouncedSearch(e.target.value);
txtSearch.onsearch = e => debouncedSearch(e.target.value);