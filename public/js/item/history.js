/* global moment, ITEM_ENTITY, ITEM_HISTORY_LIST, _, ReactDOM, React */

import app from "../_shared/app.js";
import requester from "../_shared/requester.js";

const timeline = document.getElementById("timeline");
const templateTimelineStart = document.getElementById("templateTimelineStart");
const templateTimelineItem = document.getElementById("templateTimelineItem");
const templateTimelineEmpty = document.getElementById("templateTimelineEmpty");
const templateTimelineEnd = document.getElementById("templateTimelineEnd");

const renderTimeline = history => {
  const fragment = document.createDocumentFragment();
  const timelineEmpty = document.importNode(templateTimelineEmpty.content, true);
  const timelineStart = document.importNode(templateTimelineStart.content, true);
  const timelineEnd = document.importNode(templateTimelineEnd.content, true);
  const selectedOrder = timeline.querySelector("select").value;
  const $selOrder = $(timelineStart.querySelector("select"));

  timeline.innerHTML = '';
  $selOrder.select2({width: "100%", minimumResultsForSearch: 10});
  $selOrder.select2("val", selectedOrder);
  $selOrder.on("select2:select", () => app.rebuildTooltips());

  if (!history.data.length) {
    timeline.appendChild(timelineStart);
    timeline.appendChild(timelineEmpty);
    timeline.appendChild(timelineEnd);
    app.rebuildTooltips();
    return;
  }

  history.data.map(data => {
    const timelineItem = document.importNode(templateTimelineItem.content, true);

    timelineItem.querySelector(".fas").classList.add(data.itemHistoryStockOnMove > 0 ? "fa-plus" : "fa-minus");
    timelineItem.querySelector(".fas").classList.add(data.itemHistoryStockOnMove > 0 ? "bg-success" : "bg-danger");
    timelineItem.querySelector(".fas").classList.remove(data.itemHistoryStockOnMove < 0 ? "fa-plus" : "fa-minus");
    timelineItem.querySelector(".fas").classList.remove(data.itemHistoryStockOnMove < 0 ? "bg-success" : "bg-danger");
    timelineItem.querySelector(".time").title = moment(data.itemHistoryCreatedAt).format(app.dateFormat);
    timelineItem.querySelector(".timeline-header").innerText = data.itemHistoryEventName;

    timelineItem.querySelector(".time").innerHTML = `<i class="fas fa-clock"></i>&nbsp;&nbsp;` + moment(data.itemHistoryCreatedAt).fromNow();
    timelineItem.querySelector(".timeline-body .row").innerHTML = `
      <dt class="col-sm-3">Artículos</dt>
      <dd class="col-sm-9">${data.itemHistoryStockOnMove}</dd>
      <dt class="col-sm-3">Nuevo inventario</dt>
      <dd class="col-sm-9">${data.itemHistoryNewStock}</dd>
      <dt class="col-sm-3">Nota</dt>
      <dd class="col-sm-9">${data.itemHistoryNote || `<small class="text-muted"><i>No se agregó anotación</i></small>`}</dd>
      <dt class="col-sm-3">Costo total de la operación</dt>
      <dd class="col-sm-9">${app.toCurrency(data.itemHistoryCost)}</dd>
      <dt class="col-sm-3">Precio total de la operación</dt>
      <dd class="col-sm-9">${app.toCurrency(data.itemHistoryPrice)}</dd>
      <dt class="col-sm-3">Costo por artículo</dt>
      <dd class="col-sm-9">${app.toCurrency(data.itemHistoryCost / Math.abs(data.itemHistoryStockOnMove))}</dd>
      <dt class="col-sm-3">Precio por artículo</dt>
      <dd class="col-sm-9">${app.toCurrency(data.itemHistoryPrice / Math.abs(data.itemHistoryStockOnMove))}</dd>
    `;

    fragment.appendChild(timelineItem);
  });

  timeline.appendChild(timelineStart);
  timeline.appendChild(fragment);
  timeline.appendChild(timelineEnd);
  app.rebuildTooltips();
};

class History extends React.Component {
  constructor() {
    super();
    this.state = {
      item: ITEM_ENTITY,
      history: ITEM_HISTORY_LIST,
      offset: 0,
      limit: 100,
      order: timeline.querySelector("select").value,
      allLoaded: ITEM_HISTORY_LIST.total == ITEM_HISTORY_LIST.data.length
    };
  }

  handleOrderChange() {
    timeline.onchange = async e => {
      if (e.target.matches("select")) {
        try {
          app.loading(true);

          const url = window.location.origin + window.location.pathname;
          const order = e.target.value;
          const offset = 0;
          const itemId = this.state.item.itemId;
          const limit = this.state.limit;
          const config = app.toFormData({offset, limit, order});
          const history = await requester.submitData("item-history/list/item/" + itemId, config);

          window.history.replaceState(null, '', `${url}?order=${order}`);

          this.setState({
            offset: offset,
            order: order,
            history: history.result,
            allLoaded: history.result.total == history.result.data.length
          });
        } catch (err) {
          console.log(err);
          app.renderAlert({
            autohide: false,
            container: "alert",
            message: typeof err == "string" ? err : "Intentalo de nuevo, si el error persiste contacta al administrador",
            type: "danger"
          });
        } finally {
          app.loading(false);
        }
      }
    };
  }

  handleDocumentScroll() {
    window.onscroll = _.debounce(async () => {
      const {scrollTop, clientHeight, scrollHeight} = document.documentElement;

      if (this.state.allLoaded) return;

      if (scrollTop + clientHeight >= scrollHeight - (scrollHeight * 0.10)) {
        try {
          app.loading(true);

          const order = this.state.order;
          const offset = this.state.offset + this.state.limit;
          const limit = this.state.limit;
          const itemId = this.state.item.itemId;
          const config = app.toFormData({offset, limit, order});
          const history = await requester.submitData("item-history/list/item/" + itemId, config);

          this.setState({
            offset: offset,
            allLoaded: history.result.total == history.result.data.length + this.state.history.data.length,
            history: {
              total: history.result.total,
              filtered: history.result.filtered,
              data: [...this.state.history.data, ...history.result.data]
            }
          });
        } catch (err) {
          console.log(err);
          app.renderAlert({
            autohide: false,
            container: "alert",
            message: typeof err == "string" ? err : "Intentalo de nuevo, si el error persiste contacta al administrador",
            type: "danger"
          });
        } finally {
          app.loading(false);
        }
      }
    }, 250);
  }

  componentDidMount() {
    this.handleOrderChange();
    this.handleDocumentScroll();
  }

  render() {
    renderTimeline(this.state.history);
    return null;
  }
}

ReactDOM.render(React.createElement(History), document.getElementById("storage"));
