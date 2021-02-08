/* global moment, _ */

import app from "../_shared/app.js";
import requester from "../_shared/requester.js";

const $selLength = $("#userAccessListLength");
const $selUserId = $("#userId");

const dateUserAccessFirst = document.getElementById("userAccessFirst");
const dtUserAccessList = $("#userAccessList").DataTable({
  language: app.dataTableLang,
  responsive: true,
  serverSide: true,
  processing: true,
  fixedHeader: true,
  pageLength: 10,
  dom: "tipr",
  order: [[3, "desc"]],
  columnDefs: [{
      targets: "_all",
      data: null
    }],
  ajax: async (table, setdata) => {
    try {
      const config = app.paginationConfig(table);
      config.append("userId", ($selUserId.val() || '').trim());
      config.append("sdate", dateUserAccessFirst.value.trim());
      config.append("fdate", dateUserAccessFirst.value.trim());
      const fetched = await requester.submitData("user-access/list/user-access", config);

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
      data: "userNickname"
    }, {
      data: "userName"
    }, {
      data: "userSurname"
    }, {
      name: "userAccessFirst",
      render: data => !data.userAccessFirst ? `<small class="text-muted"><i>Sin registro</i></small>` : `
        <span title="${moment(data.userAccessFirst).fromNow()} (${moment(data.userAccessFirst).format(app.dateFormat)})">
          ${data.userAccessFirst}
        </span>`
    }, {
      name: "userAccessLast",
      render: data => !data.userAccessLast ? `<small class="text-muted"><i>Sin registro</i></small>` : `
        <span title="${moment(data.userAccessLast).fromNow()} (${moment(data.userAccessLast).format(app.dateFormat)})">
          ${data.userAccessLast}
        </span>`
    }]
});

const limit = 10;
const initSelect2 = () => {
  $selUserId.select2({
    width: "100%",
    placeholder: "Selecciona un usuario",
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
          results: [{
              id: 0,
              text: "Todos los usuarios"
            }].concat(result.data.map(user => {
            return {
              id: user.userId,
              text: `${user.userName} ${user.userSurname}`
            };
          })),
          pagination: {
            more: (params.page * limit) < result.filtered
          }
        };
      },
      transport: async function (params, setdata) {
        try {
          const config = new FormData();
          config.append("column", "userName,userSurname");
          config.append("order", "asc,asc");
          config.append("needle", params.data.needle);
          config.append("limit", limit);
          config.append("offset", (params.data.page - 1) * limit);
          const fetched = await requester.submitData("user/list/active-users", config);
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

  $selUserId.on("select2:select", () => {
    dtUserAccessList.ajax.reload();
    app.rebuildTooltips();
  });

  app.rebuildTooltips();
};

initSelect2();
$selUserId.append(new Option("Todos los usuarios", 0, true, true)).trigger("change");
$selUserId.on("select2:close", () => initSelect2());

$selLength.select2({width: "100%", minimumResultsForSearch: 10});
$selLength.on("select2:select", e => {
  dtUserAccessList.page.len(e.target.value).draw();
  app.rebuildTooltips();
});

dtUserAccessList.on("responsive-display", () => app.rebuildTooltips());
dtUserAccessList.on("draw", () => app.rebuildTooltips());
dateUserAccessFirst.onchange = () => dtUserAccessList.ajax.reload();