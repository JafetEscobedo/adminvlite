/* global moment, _ */

import app from "../_shared/app.js";
import requester from "../_shared/requester.js";

const $selLength = $("#usersListLength");
const txtSearch = document.getElementById("usersListSearch");
const dtUsersList = $("#usersList").DataTable({
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
      targets: [7],
      orderable: false
    }],
  ajax: async (table, setdata) => {
    try {
      const config = app.paginationConfig(table);
      const fetched = await requester.submitData("user/list/users", config);
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
      data: "userName"
    }, {
      data: "userSurname"
    }, {
      name: "userActive",
      render: data => data.userActive == 'y'
         ? `<span class="badge badge-success">Activo</span>`
         : `<span class="badge badge-danger">Inactivo</span>`
    }, {
      data: "userNickname"
    }, {
      data: "userRoleName"
    }, {
      name: "userCreatedAt",
      render: data => `
        <span title="${moment(data.userCreatedAt).format(app.dateFormat)}">
          ${moment(data.userCreatedAt).fromNow()}
        </span>`
    }, {
      name: "userUpdatedAt",
      render: data => `
        <span title="${moment(data.userUpdatedAt).format(app.dateFormat)}">
          ${moment(data.userUpdatedAt).fromNow()}
        </span>`
    }, {
      name: "userInactivatedAt",
      render: data => !data.userInactivatedAt ? `<small class="text-muted"><i>No aplica</i></small>` : `
        <span title="${moment(data.userInactivatedAt).format(app.dateFormat)}">
          ${moment(data.userInactivatedAt).fromNow()}
        </span>`
    }, {
      render: data => `
        <a title="Actualizar" class="btn btn-xs btn-default" href="${app.url("user/view/users-list/update/" + data.userId)}")">
          <i class="fas fa-fw fa-pencil-alt text-info"></i>
        </a>`
    }]
});

const debouncedSearch = _.debounce(needle => dtUsersList.search(needle).draw(), 250);

$selLength.select2({width: "100%", minimumResultsForSearch: 10});
$selLength.on("select2:select", e => {
  dtUsersList.page.len(e.target.value).draw();
  app.rebuildTooltips();
});

dtUsersList.on("responsive-display", () => app.rebuildTooltips());
dtUsersList.on("draw", () => app.rebuildTooltips());
txtSearch.onkeyup = e => debouncedSearch(e.target.value);
txtSearch.onsearch = e => debouncedSearch(e.target.value);