/* global moment, _ */

import app from "../_shared/app.js";
import requester from "../_shared/requester.js";

const $selLength = $("#userRolesListLength");
const txtSearch = document.getElementById("userRolesListSearch");
const dtUserRolesList = $("#userRolesList").DataTable({
  language: app.dataTableLang,
  responsive: true,
  serverSide: true,
  processing: true,
  fixedHeader: true,
  pageLength: 10,
  dom: "tipr",
  order: [[0, "asc"]],
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
      const fetched = await requester.submitData("user-role/list/user-roles", config);

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
        message: typeof err === "string" ? err : "Intentalo de nuevo, si el error persiste contacta al administrador",
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
      data: "userRoleName"
    }, {
      name: "userRoleActive",
      render: data => data.userRoleActive === 'y'
                ? `<span class="badge badge-success">Activo</span>`
                : `<span class="badge badge-danger">Inactivo</span>`
    }, {
      name: "userRoleAccess",
      render: data => `
        <span title="${data.userRoleAccess.replace(/[\[\]\\"]/gi, '')}" style="max-width: 200px" class="d-block text-truncate">
          ${data.userRoleAccess.replace(/[\[\]\\]/gi, '')}
        </span>`
    }, {
      name: "userRoleCreatedAt",
      render: data => `
        <span title="${moment(data.userRoleCreatedAt).format(app.dateFormat)}">
          ${moment(data.userRoleCreatedAt).fromNow()}
        </span>`
    }, {
      name: "userRoleUpdatedAt",
      render: data => `
        <span title="${moment(data.userRoleUpdatedAt).format(app.dateFormat)}">
          ${moment(data.userRoleUpdatedAt).fromNow()}
        </span>`
    }, {
      name: "userRoleInactivatedAt",
      render: data => !data.userRoleInactivatedAt ? `<small class="text-muted"><i>No aplica</i></small>` : `
        <span title="${moment(data.userRoleInactivatedAt).format(app.dateFormat)}">
          ${moment(data.userRoleInactivatedAt).fromNow()}
        </span>`
    }, {
      render: data => `
        <a title="Actualizar" class="btn btn-xs bg-gradient-info" href="${app.url("user-role/view/user-roles-list/update/" + data.userRoleId)}")">
          <i class="fas fa-fw fa-pencil-alt"></i>
        </a>`
    }]
});

const debouncedSearch = _.debounce(needle => dtUserRolesList.search(needle).draw(), 250);

$selLength.select2({width: "100%", minimumResultsForSearch: 10});
$selLength.on("select2:select", e => {
  dtUserRolesList.page.len(e.target.value).draw();
  app.rebuildTooltips();
});

dtUserRolesList.on("responsive-display", () => app.rebuildTooltips());
dtUserRolesList.on("draw", () => app.rebuildTooltips());
txtSearch.onkeyup = e => debouncedSearch(e.target.value);
txtSearch.onsearch = e => debouncedSearch(e.target.value);