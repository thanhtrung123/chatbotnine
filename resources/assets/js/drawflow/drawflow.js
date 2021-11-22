// MIT License

// Copyright (c) 2020 Jero Soler

// Permission is hereby granted, free of charge, to any person obtaining a copy
// of this software and associated documentation files (the "Software"), to deal
// in the Software without restriction, including without limitation the rights
// to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
// copies of the Software, and to permit persons to whom the Software is
// furnished to do so, subject to the following conditions:

// The above copyright notice and this permission notice shall be included in all
// copies or substantial portions of the Software.

// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
// IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
// FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
// AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
// LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
// OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
// SOFTWARE.
//"use strict";
// https://tc39.github.io/ecma262/#sec-array.prototype.findIndex
if (!Array.prototype.findIndex) {
  Object.defineProperty(Array.prototype, 'findIndex', {
    value: function(predicate) {
     // 1. Let O be ? ToObject(this value).
      if (this == null) {
        throw new TypeError('"this" is null or not defined');
      }

      var o = Object(this);

      // 2. Let len be ? ToLength(? Get(O, "length")).
      var len = o.length >>> 0;

      // 3. If IsCallable(predicate) is false, throw a TypeError exception.
      if (typeof predicate !== 'function') {
        throw new TypeError('predicate must be a function');
      }

      // 4. If thisArg was supplied, let T be thisArg; else let T be undefined.
      var thisArg = arguments[1];

      // 5. Let k be 0.
      var k = 0;

      // 6. Repeat, while k < len
      while (k < len) {
        // a. Let Pk be ! ToString(k).
        // b. Let kValue be ? Get(O, Pk).
        // c. Let testResult be ToBoolean(? Call(predicate, T, « kValue, k, O »)).
        // d. If testResult is true, return k.
        var kValue = o[k];
        if (predicate.call(thisArg, kValue, k, o)) {
          return k;
        }
        // e. Increase k by 1.
        k++;
      }

      // 7. Return -1.
      return -1;
    }
  });
}
if (!Object.entries) {
  Object.entries = function (obj) {
    var ownProps = Object.keys(obj),
      i = ownProps.length,
      resArray = new Array(i); // preallocate the Array
    while (i--)
      resArray[i] = [ownProps[i], obj[ownProps[i]]];

    return resArray;
  };
}
function _instanceof(left, right) { if (right != null && typeof Symbol !== "undefined" && right[Symbol.hasInstance]) { return !!right[Symbol.hasInstance](left); } else { return left instanceof right; } }

function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { ownKeys(Object(source), true).forEach(function (key) { _defineProperty(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

function _classCallCheck(instance, Constructor) { if (!_instanceof(instance, Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

window.Drawflow = /*#__PURE__*/function  () {
  function Drawflow(container) {
    var render = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;

    _classCallCheck(this, Drawflow);

    this.events = {};
    this.container = container;
    this.precanvas = null;
    this.precanvas_wrap = null;
    this.nodeId = 1;
    this.ele_selected = null;
    this.node_selected = null;
    this.drag = false;
    this.reroute = false;
    this.reroute_fix_curvature = false;
    this.curvature = 0.5;
    this.reroute_curvature_start_end = 0.5;
    this.reroute_curvature = 0.5;
    this.reroute_width = 6;
    this.drag_point = false;
    this.editor_selected = false;
    this.connection = false;
    this.connection_ele = null;
    this.connection_selected = null;
    this.canvas_x = 0;
    this.canvas_y = 0;
    this.pos_x = 0;
    this.pos_y = 0;
    this.mouse_x = 0;
    this.mouse_y = 0;
    this.line_path = 5;
    this.first_click = null;
    this.force_first_input = false;
    this.select_elements = null;
    this.noderegister = {};
    this.render = render;
    this.drawflow = {
      "drawflow": {
        "Home": {
          "data": {}
        }
      }
    }; // Configurable options

    this.module = 'Home';
    this.editor_mode = 'edit';
    this.zoom = 1;
    this.zoom_max = 1.6;
    this.zoom_min = 0.11; // Mobile

    this.evCache = new Array();
    this.movingX = false;
    this.movingY = false;
    this.autoSpeedX = 0;
    this.autoSpeedY = 0;
    this.autoRange = 100;
    this.timestampX = 0;
    this.timestampY = 0;
    this.prevDiff = -1;

    this.history = [];
    this.maximumHistories = 11;
    this.currentHitoryIndex = 0;
    this.preventHistoryEvent = false;
  }

  _createClass(Drawflow, [{
    key: "start",
    value: function start() {
      // console.info("Start Drawflow!!");
      this.container.classList.add("parent-drawflow");
      this.container.tabIndex = 0;
      this.precanvas = document.createElement('div');
      this.precanvas.classList.add("drawflow");
      this.precanvas_wrap = document.createElement('div');
      this.precanvas_wrap.classList.add("drawflow_wrap");
      this.container.appendChild(this.precanvas_wrap);
      this.precanvas_wrap.appendChild(this.precanvas);
      /* Mouse and Touch Actions */

      this.container.addEventListener('mouseup', this.dragEnd.bind(this));
      this.container.addEventListener('mousemove', this.position.bind(this));
      this.container.addEventListener('mousedown', this.click.bind(this));
      this.container.addEventListener('touchend', this.dragEnd.bind(this));
      this.container.addEventListener('touchmove', this.position.bind(this));
      this.container.addEventListener('touchstart', this.click.bind(this));
      /* Context Menu */

      this.container.addEventListener('contextmenu', this.contextmenu.bind(this));
      /* Delete */

      this.container.addEventListener('keydown', this.key.bind(this));
      /* Zoom Mouse */

      this.container.addEventListener('wheel', this.zoom_enter.bind(this));
      /* Update data Nodes */

      this.container.addEventListener('input', this.updateNodeValue.bind(this));
      //this.container.addEventListener('dblclick', this.dblclick.bind(this));
      /* Mobile zoom */

      this.container.onpointerdown = this.pointerdown_handler.bind(this);
      this.container.onpointermove = this.pointermove_handler.bind(this);
      this.container.onpointerup = this.pointerup_handler.bind(this);
      this.container.onpointercancel = this.pointerup_handler.bind(this);
      this.container.onpointerout = this.pointerup_handler.bind(this);
      this.container.onpointerleave = this.pointerup_handler.bind(this);
      this.clearHistory();
      this.load();
      this.addHistory();
    }
    /* Mobile zoom */

  }, {
    key: "pointerdown_handler",
    value: function pointerdown_handler(ev) {
      this.evCache.push(ev);
    }
  }, {
    key: "pointermove_handler",
    value: function pointermove_handler(ev) {
      for (var i = 0; i < this.evCache.length; i++) {
        if (ev.pointerId == this.evCache[i].pointerId) {
          this.evCache[i] = ev;
          break;
        }
      }

      if (this.evCache.length == 2) {
        // Calculate the distance between the two pointers
        var curDiff = Math.abs(this.evCache[0].clientX - this.evCache[1].clientX);

        if (this.prevDiff > 100) {
          if (curDiff > this.prevDiff) {
            // The distance between the two pointers has increased
            this.zoom_in();
          }

          if (curDiff < this.prevDiff) {
            // The distance between the two pointers has decreased
            this.zoom_out();
          }
        }

        this.prevDiff = curDiff;
      }
    }
  }, {
    key: "pointerup_handler",
    value: function pointerup_handler(ev) {
      this.remove_event(ev);

      if (this.evCache.length < 2) {
        this.prevDiff = -1;
      }
    }
  }, {
    key: "remove_event",
    value: function remove_event(ev) {
      // Remove this event from the target's cache
      for (var i = 0; i < this.evCache.length; i++) {
        if (this.evCache[i].pointerId == ev.pointerId) {
          this.evCache.splice(i, 1);
          break;
        }
      }
    }
    /* End Mobile Zoom */
  }, {
    key: "contextmenuDel",
    value: function contextmenuDel() {
      if (this.precanvas.getElementsByClassName("drawflow-delete").length) {
        this.precanvas.getElementsByClassName("drawflow-delete")[0].parentElement.removeChild(this.precanvas.getElementsByClassName("drawflow-delete")[0]);
      }
    }
  }, {
    key: "load",
    value: function load() {
      this.preventHistoryEvent = true;
      for (var key in this.drawflow.drawflow[this.module].data) {
        this.addNodeImport(this.drawflow.drawflow[this.module].data[key], this.precanvas);
      }

      if (this.reroute) {
        for (var key in this.drawflow.drawflow[this.module].data) {
          this.addRerouteImport(this.drawflow.drawflow[this.module].data[key]);
        }
      }

      for (var key in this.drawflow.drawflow[this.module].data) {
        this.updateConnectionNodes('node-' + key);
      }

      var editor = this.drawflow.drawflow;
      var number = 1;
      Object.keys(editor).map(function (moduleName, index) {
        Object.keys(editor[moduleName].data).map(function (id, index2) {
          if (parseInt(id) >= number) {
            number = parseInt(id) + 1;
          }
        });
      });
      this.nodeId = number;
      this.update_container_size(null, true);

      var data = this.drawflow.drawflow[this.module].data,
          dataQa = new Array(),
          dataScenario = new Array();
      $.each(data, function(index, value) {
        if (index.substr(0, 1) == 'q') {
          dataQa.push(value);
        }
        if (index.substr(0, 1) == 's') {
          dataScenario.push(value);
        }
      });
      dataQa.sort(function (a, b) {
        if (a.pos_x == b.pos_x) {
          return a.pos_y - b.pos_y;
        }
        return a.pos_x - b.pos_x;
      });
      dataScenario.sort(function (a, b) {
        if (a.pos_x == b.pos_x) {
          return a.pos_y - b.pos_y;
        }
        return a.pos_x - b.pos_x;
      });
      let data_drawflow = dataQa.concat(dataScenario);
      if (data_drawflow.length > 0) {
        var obj_data_drawflow = {};
        $.each(data_drawflow, function (index, value) {
          obj_data_drawflow[value.id] = value;
        });
        this.drawflow.drawflow[this.module].data = obj_data_drawflow;
      }
      this.showDataToLeft();
      this.preventHistoryEvent = false;
    }
  }, {
    key: "removeReouteConnectionSelected",
    value: function removeReouteConnectionSelected() {
      if (this.reroute_fix_curvature) {
        this.connection_selected.parentElement.querySelectorAll(".main-path").forEach(function (item, i) {
          item.classList.remove("selected");
        });
      }
    }
  }, {
    key: "closest",
    value: function closest(el, selector) {
      var matchesFn;

      // find vendor prefix
      ['matches', 'webkitMatchesSelector', 'mozMatchesSelector', 'msMatchesSelector', 'oMatchesSelector'].some(function (fn) {
        if (typeof document.body[fn] == 'function') {
          matchesFn = fn;
          return true;
        }
        return false;
      })

      var parent;

      // traverse parents
      while (el) {
        parent = el.parentElement;
        if (parent && parent[matchesFn](selector)) {
          return parent;
        }
        el = parent;
      }

      return null;
    }
  }, {
    key: "click",
    value: function click(e) {
      var names_arr = ['output', 'parent-drawflow', 'drawflow', 'main-path', 'point', 'drawflow-delete'];
      if (this.editor_mode === 'fixed') {
        //return false;
        if (e.target.classList[0] === 'parent-drawflow' || e.target.classList[0] === 'drawflow') {
          this.ele_selected = e.target.closest(".parent-drawflow");
        } else {
          return false;
        }
      } else {
        this.first_click = e.target;
        this.ele_selected = e.target;

        if (e.button === 0) {
          this.contextmenuDel();
        }
      }

      if (names_arr.indexOf(e.target.getAttribute('class')) != -1) {
        this.ele_selected = e.target;
      } else {
        if (e.target.parentNode.getAttribute('class') == 'drawflow') {
          this.ele_selected = e.target;
        }
        if (e.target.parentNode.parentNode.getAttribute('class') == 'drawflow_content_node' && e.target.parentNode.parentNode != null) {
          this.ele_selected = e.target.parentNode.parentNode.parentNode;
        }
      }
      var classList = this.ele_selected ? this.ele_selected.getAttribute('class').split(" ") : [];
      if (this.ele_selected && classList[1]) {
        if (classList[1]){
          if (classList[1] == 'editor-scenario') {
            $('.tabscenario').addClass('active');
            $('.tabqa').removeClass('active');
            $('.tabscenario').children().attr('aria-expanded', true);
            $('.tabqa').children().attr('aria-expanded', false);
            $('#scenario').addClass('active');
            $('#QA').removeClass('active');
          }
          if (classList[1] == 'editor-qa') {
            $('.tabscenario').removeClass('active');
            $('.tabqa').addClass('active');
            $('.tabscenario').children().attr('aria-expanded', false);
            $('.tabqa').children().attr('aria-expanded', true);
            $('#scenario').removeClass('active');
            $('#QA').addClass('active');
          }
        }
      }
      $('.connection').map(function(i, val) {
        if (val && val.childNodes.length > 0 && val.childNodes[0].tagName == 'path') {
            val.childNodes[0].setAttribute("class", val.childNodes[0].getAttribute("class").replace(" target-trigger", ""))
            $(val).css('z-index', '0');
        }
      });
      // console.log(this.ele_selected)
      switch (classList[0]) {
        case 'drawflow-node':
          if (this.node_selected != null) {
            //this.node_selected.classList.remove("selected");
            $(this.node_selected).removeClass("selected");
          }

          if (this.connection_selected != null) {
            //this.connection_selected.classList.remove("selected");
            $(this.connection_selected).removeClass("selected");
            this.removeReouteConnectionSelected();
            this.connection_selected = null;
          }

          this.dispatch('nodeSelected', this.ele_selected.id.slice(5));
          this.node_selected = this.ele_selected;
          //this.node_selected.classList.add("selected");
          $(this.connection_selected).addClass("selected");
          this.drag = true;
          this.focusData(this.ele_selected, true);
          break;

        case 'output':
          this.connection = true;

          if (this.node_selected != null) {
            // this.node_selected.classList.remove("selected");
            $(this.node_selected).removeClass("selected");
            this.node_selected = null;
          }

          if (this.connection_selected != null) {
            // this.connection_selected.classList.remove("selected");
            $(this.connection_selected).removeClass("selected");
            this.removeReouteConnectionSelected();
            this.connection_selected = null;
          }

          this.drawConnection(e.target);
          break;

        case 'drawflow_wrap':
        case 'parent-drawflow':
        case 'drawflow':
          //remove css svg and css target
          $('.drawflow').find('.selected').removeClass(' selected');
          $('.content-data').find('.focus-data').removeClass(' focus-data');
          if (this.node_selected != null) {
            // this.node_selected.classList.remove("selected");
            $(this.node_selected).removeClass("selected");
            this.node_selected = null;
          }

          if (this.connection_selected != null) {
            // this.connection_selected.classList.remove("selected");
            $(this.connection_selected).removeClass("selected");
            this.removeReouteConnectionSelected();
            this.connection_selected = null;
          }
          break;

        case 'main-path':
          var id = $('.edit-btn-scenario').val();
          $('.edit-btn-scenario').val('');
          if (id) {
            $('.' + id).attr('class', $('.' + id).attr('class').replace(' focus-data', ''));
          }
          if (this.node_selected != null) {
            // this.node_selected.classList.remove("selected");
            $(this.node_selected).removeClass("selected");
            this.node_selected = null;
          }

          if (this.connection_selected != null) {
            // this.connection_selected.classList.remove("selected");
            $(this.connection_selected).removeClass("selected");
            this.removeReouteConnectionSelected();
            this.connection_selected = null;
          }

          this.connection_selected = this.ele_selected;
          // this.connection_selected.classList.add("selected");
          $(this.connection_selected).addClass("selected");

          if (this.reroute_fix_curvature) {
            this.connection_selected.parentElement.querySelectorAll(".main-path").forEach(function (item, i) {
              // item.classList.add("selected");
              $(item).addClass("selected");
            });
          }

          break;

        case 'point':
          this.drag_point = true;
          // this.ele_selected.classList.add("selected");
          $(this.ele_selected).addClass("selected");
          break;

        case 'drawflow-delete':
          if (this.node_selected) {
            this.removeNodeId(this.node_selected.id);
          }

          if (this.connection_selected) {
            this.removeConnection();
          }

          if (this.node_selected != null) {
            this.node_selected.classList.remove("selected");
            this.node_selected = null;
          }

          if (this.connection_selected != null) {
            // this.connection_selected.classList.remove("selected");
            $(this.connection_selected).removeClass("selected");
            this.removeReouteConnectionSelected();
            this.connection_selected = null;
          }

          break;

        default:
      }

      if (e.type === "touchstart") {
        this.pos_x = e.touches[0].clientX;
        this.pos_y = e.touches[0].clientY;
      } else {
        this.pos_x = e.clientX;
        this.pos_y = e.clientY;
      }
    }
  }, {
    key: "autoMoveRight",
    value: function autoMoveRight() {
      if (!this.movingX) {
        this.movingX = 1;
        window.requestAnimationFrame(this.autoMoveX.bind(this));
      }
    }
  }, {
    key: "autoMoveLeft",
    value: function autoMoveLeft() {
      if (!this.movingX) {
        this.movingX = -1;
        window.requestAnimationFrame(this.autoMoveX.bind(this));
      }
    }
  }, {
    key: "autoMoveX",
    value: function autoMoveX(timestamp) {
      if (!this.movingX) return;
      if (!this.timestampX) {
        this.timestampX = timestamp;
      } else {
        var move_space = Math.round(((timestamp - this.timestampX) * this.autoSpeedX / 1000) * this.movingX);
        if (this.container.scrollLeft + move_space < 0) {
          move_space = -this.container.scrollLeft;
        } else if (this.container.scrollLeft + move_space > this.container.scrollWidth - this.container.clientWidth) {
          move_space = this.container.scrollWidth - this.container.clientWidth - this.container.scrollLeft;
        }
        if (this.container.scrollLeft + move_space > 0) {
          this.ele_selected.style.left = (this.ele_selected.offsetLeft + (move_space / this.zoom)) + 'px';
          this.container.scrollLeft += move_space;
          this.timestampX = timestamp;
          //Update connection
          this.updateConnectionNodes(this.ele_selected.id, this.pos_x, this.pos_y);
        }
      }
      if (this.movingX) window.requestAnimationFrame(this.autoMoveX.bind(this));
    }
  }, {
    key: "autoMoveBottom",
    value: function autoMoveBottom() {
      if (!this.movingY) {
        this.movingY = 1;
        window.requestAnimationFrame(this.autoMoveY.bind(this));
      }
    }
  }, {
    key: "autoMoveTop",
    value: function autoMoveTop() {
      if (!this.movingY) {
        this.movingY = -1;
        window.requestAnimationFrame(this.autoMoveY.bind(this));
      }
    }
  }, {
    key: "autoMoveY",
    value: function autoMoveY(timestamp) {
      if (!this.movingY) return;
      if (!this.timestampY) {
        this.timestampY = timestamp;
      } else {
        var move_space = Math.round(((timestamp - this.timestampY) * this.autoSpeedY / 1000) * this.movingY);
        if (this.container.scrollTop + move_space < 0) {
          move_space = -this.container.scrollTop;
        } else if (this.container.scrollTop + move_space > this.container.scrollHeight - this.container.clientHeight) {
          move_space = this.container.scrollHeight - this.container.clientHeight - this.container.scrollTop;
        }
        if (this.container.scrollTop + move_space > 0) {
          this.ele_selected.style.top = (this.ele_selected.offsetTop + (move_space / this.zoom)) + 'px';
          this.container.scrollTop += move_space;
          this.timestampY = timestamp;
          //Update connection
          this.updateConnectionNodes(this.ele_selected.id, this.pos_x, this.pos_y);
        }
      }
      if (this.movingY) window.requestAnimationFrame(this.autoMoveY.bind(this));
    }
  }, {
    key: "position",
    value: function position(e) {
      if (e.type === "touchmove") {
        var e_pos_x = e.touches[0].clientX;
        var e_pos_y = e.touches[0].clientY;
      } else {
        var e_pos_x = e.clientX;
        var e_pos_y = e.clientY;
      }

      if (this.connection) {
        this.updateConnection(e_pos_x, e_pos_y);
      }

      if (this.editor_selected) {
        /*if (e.ctrlKey) {
          this.selectElements(e_pos_x, e_pos_y);
        } else { */
        x = this.canvas_x + -(this.pos_x - e_pos_x);
        y = this.canvas_y + -(this.pos_y - e_pos_y); // console.log(canvas_x +' - ' +pos_x + ' - '+ e_pos_x + ' - ' + x);

        this.dispatch('translate', {
          x: x,
          y: y
        });
        this.precanvas.style.transform = "translate(" + x + "px, " + y + "px) scale(" + this.zoom + ")"; //}
      }

      if (this.drag) {
        var x = (this.pos_x - e_pos_x) * this.precanvas.clientWidth / (this.precanvas.clientWidth * this.zoom);
        var y = (this.pos_y - e_pos_y) * this.precanvas.clientHeight / (this.precanvas.clientHeight * this.zoom);
        this.pos_x = e_pos_x;
        this.pos_y = e_pos_y;
        this.ele_selected.style.top = this.ele_selected.offsetTop - y + "px";
        this.ele_selected.style.left = this.ele_selected.offsetLeft - x + "px";
        this.drawflow.drawflow[this.module].data[this.ele_selected.id.slice(5)].pos_x = this.ele_selected.offsetLeft - x;
        this.drawflow.drawflow[this.module].data[this.ele_selected.id.slice(5)].pos_y = this.ele_selected.offsetTop - y;
        this.updateConnectionNodes(this.ele_selected.id, e_pos_x, e_pos_y);

        var container_rect = this.container.getBoundingClientRect();
        if (e.clientX > container_rect.right - this.autoRange) {
          this.autoSpeedX = (e.clientX - (container_rect.right - this.autoRange)) * 10;
          this.autoMoveRight();
        } else if (e.clientX < container_rect.left + this.autoRange) {
          this.autoSpeedX = ((container_rect.left + this.autoRange) - e.clientX) * 10;
          this.autoMoveLeft();
        } else {
          this.movingX = 0;
        }
        // console.log(e.clientY,container_rect.top,container_rect.bottom)
        if (e.clientY > container_rect.bottom - this.autoRange) {
          this.autoSpeedY = (e.clientY - (container_rect.bottom - this.autoRange)) * 10;
          this.autoMoveBottom();
        } else if (e.clientY < container_rect.top + this.autoRange) {
          this.autoSpeedY = ((container_rect.top + this.autoRange) - e.clientY) * 10;
          this.autoMoveTop();
        } else {
          this.movingY = 0;
        }
      }

      if (this.drag_point) {
        var x = (this.pos_x - e_pos_x) * this.precanvas.clientWidth / (this.precanvas.clientWidth * this.zoom);
        var y = (this.pos_y - e_pos_y) * this.precanvas.clientHeight / (this.precanvas.clientHeight * this.zoom);
        this.pos_x = e_pos_x;
        this.pos_y = e_pos_y;
        var pos_x = this.pos_x * (this.precanvas.clientWidth / (this.precanvas.clientWidth * this.zoom)) - this.precanvas.getBoundingClientRect().x * (this.precanvas.clientWidth / (this.precanvas.clientWidth * this.zoom));
        var pos_y = this.pos_y * (this.precanvas.clientHeight / (this.precanvas.clientHeight * this.zoom)) - this.precanvas.getBoundingClientRect().y * (this.precanvas.clientHeight / (this.precanvas.clientHeight * this.zoom));
        this.ele_selected.setAttributeNS(null, 'cx', pos_x);
        this.ele_selected.setAttributeNS(null, 'cy', pos_y);
        var nodeUpdate = this.ele_selected.parentElement.classList[2].slice(9);
        var nodeUpdateIn = this.ele_selected.parentElement.classList[1].slice(13);
        var output_class = this.ele_selected.parentElement.classList[3];
        var input_class = this.ele_selected.parentElement.classList[4];
        var numberPointPosition = Array.from(this.ele_selected.parentElement.children).indexOf(this.ele_selected) - 1;

        if (this.reroute_fix_curvature) {
          var numberMainPath = this.ele_selected.parentElement.querySelectorAll(".main-path").length - 1;
          numberPointPosition -= numberMainPath;

          if (numberPointPosition < 0) {
            numberPointPosition = 0;
          }
        }

        var nodeId = nodeUpdate.slice(5);
        var searchConnection = this.drawflow.drawflow[this.module].data[nodeId].outputs[output_class].connections.findIndex(function (item, i) {
          return item.node === nodeUpdateIn && item.output === input_class;
        });
        this.drawflow.drawflow[this.module].data[nodeId].outputs[output_class].connections[searchConnection].points[numberPointPosition] = {
          pos_x: pos_x,
          pos_y: pos_y
        };
        var parentSelected = this.ele_selected.parentElement.classList[2].slice(9);
        /*this.drawflow.drawflow[this.module].data[this.ele_selected.id.slice(5)].pos_x = (this.ele_selected.offsetLeft - x);
        this.drawflow.drawflow[this.module].data[this.ele_selected.id.slice(5)].pos_y = (this.ele_selected.offsetTop - y);
        */

        this.updateConnectionNodes(parentSelected, e_pos_x, e_pos_y);
      }

      if (e.type === "touchmove") {
        this.mouse_x = e_pos_x;
        this.mouse_y = e_pos_y;
      }

      this.dispatch('mouseMove', {
        x: e_pos_x,
        y: e_pos_y
      });
    }
  }, {
    key: "update_container_size",
    value: function update_container_size(callback,fit) {
      var overflow_w = 0,
        overflow_h = 0,
        zoom = this.zoom,
        precanvas_wrap = $(this.precanvas_wrap),
        container = $(this.container);
        if (Object.keys(this.drawflow.drawflow[this.module].data).length === 0) {
          precanvas_wrap.css({
            width: '100%',
            height: '100%'
          });
        } else {
          container.find('.drawflow-node').each(function () {
            if (fit){
              overflow_w = Math.max(overflow_w,$(this).position().left + ( $(this).outerWidth() * zoom ) + 50);
              overflow_h = Math.max(overflow_h,$(this).position().top + ( $(this).outerHeight() * zoom ) + 50);
            }else{
              if ($(this).position().top + ($(this).outerHeight() * zoom) + 50 > precanvas_wrap.outerHeight()) {
                overflow_h = container.outerHeight();
              }
              if ($(this).position().left + ($(this).outerWidth() * zoom) + 50 > precanvas_wrap.outerWidth()) {
                overflow_w = container.outerWidth();
              }
            }
          });
          if (overflow_h || overflow_w) {
            //console.log(fit,overflow_h,overflow_w)
            if (fit){
              precanvas_wrap.css({
                width: this.change_full_px_to_100_percent(overflow_w + this.canvas_x, container.outerWidth()),
                height: this.change_full_px_to_100_percent(overflow_h + this.canvas_y, container.outerHeight())
              });
            }else{
              precanvas_wrap.css({
                width: this.change_full_px_to_100_percent(precanvas_wrap.outerWidth() + overflow_w, container.outerWidth()),
                height: this.change_full_px_to_100_percent(precanvas_wrap.outerHeight() + overflow_h, container.outerHeight())
              });
              this.update_container_size();
            }
            if (callback) callback();
          }
        }
    }
  },{
    key: "change_full_px_to_100_percent",//Misc function
    value: function change_full_px_to_100_percent(new_value,current_value) {
      return current_value >= new_value ? '100%' : new_value
    }
  }, {
    key: "dragEnd",
    value: function dragEnd(e) {
      if (this.select_elements != null) {
        this.select_elements.parentNode.removeChild(this.select_elements)
        this.select_elements = null;
      }

      if (e.type === "touchend") {
        var e_pos_x = this.mouse_x;
        var e_pos_y = this.mouse_y;
        var ele_last = document.elementFromPoint(e_pos_x, e_pos_y);
      } else {
        var e_pos_x = e.clientX;
        var e_pos_y = e.clientY;
        var ele_last = e.target;
      }

      if (this.drag) {
        var elem = $(this.ele_selected),
          container = $(this.container);
        this.update_container_size(function () {
          container.animate({
            scrollTop: (elem.position().top * 1) - ((container.outerHeight() - elem.height()) / 2),
            scrollLeft: (elem.position().left * 1) - ((container.outerWidth() - elem.width()) / 2)
          }, {
              duration: 300,
            });
        });

        this.dispatch('nodeMoved', this.ele_selected.id.slice(5));
        this.addHistory();
      }

      if (this.drag_point) {
        this.ele_selected.classList.remove("selected");
      }

      if (this.editor_selected) {
        this.canvas_x = this.canvas_x + -(this.pos_x - e_pos_x);
        this.canvas_y = this.canvas_y + -(this.pos_y - e_pos_y);
        this.editor_selected = false;
      }

      if (this.connection === true) {
        //console.log(ele_last);
        var classList = ele_last.getAttribute('class').split(" ");
        if (classList[0] === 'input' || this.force_first_input && (ele_last.closest(".drawflow_content_node") != null || classList[0] === 'drawflow-node')) {
          if (this.force_first_input && (ele_last.closest(".drawflow_content_node") != null || classList[0] === 'drawflow-node')) {
            if (ele_last.closest(".drawflow_content_node") != null) {
              var input_id = ele_last.closest(".drawflow_content_node").parentElement.id;
            } else {
              var input_id = ele_last.id;
            }

            var input_class = "input_1";
          } else {
            // Fix connection;
            var input_id = ele_last.parentElement.parentElement.id;
            var input_class = ele_last.classList[1];
          }

          if (this.ele_selected == null) {
            return false;
          }

          var output_id = this.ele_selected.parentElement.parentElement.id;
          var output_class = this.ele_selected.classList[1];

          if (output_id !== input_id) {
            if (this.container.querySelectorAll('.connection.node_in_' + input_id + '.node_out_' + output_id + '.' + output_class + '.' + input_class).length === 0) {
              // Conection no exist save connection
              this.connection_ele.setAttribute('class', 'connection' + ' node_in_' + input_id + ' node_out_' + output_id + ' ' + output_class + ' ' + input_class);
              // this.connection_ele.classList.add("node_in_" + input_id);
              // this.connection_ele.classList.add("node_out_" + output_id);
              // this.connection_ele.classList.add(output_class);
              // this.connection_ele.classList.add(input_class);
              var id_input = input_id.slice(5);
              var id_output = output_id.slice(5);
              arr_insert_relation.push(id_input + ' ' + id_output);
              this.drawflow.drawflow[this.module].data[id_output].outputs[output_class].connections.push({
                "node": id_input,
                "output": input_class
              });
              this.drawflow.drawflow[this.module].data[id_input].inputs[input_class].connections.push({
                "node": id_output,
                "input": output_class
              });
              this.updateConnectionNodes('node-' + id_output);
              this.updateConnectionNodes('node-' + id_input);
              this.dispatch('connectionCreated', {
                output_id: id_output,
                input_id: id_input,
                output_class: output_class,
                input_class: input_class
              });
            } else {
              this.connection_ele.parentNode.removeChild(this.connection_ele);
            }

            this.connection_ele = null;
            this.addHistory();
          } else {
            // Connection exists Remove Connection;
            this.connection_ele.parentNode.removeChild(this.connection_ele);
            this.connection_ele = null;
          }
        } else {
          // Remove Connection;
          if (this.connection_ele != null) {
            this.connection_ele.parentNode.removeChild(this.connection_ele)
          }
          this.connection_ele = null;
        }
      }

      this.drag = false;
      this.drag_point = false;
      this.connection = false;
      this.ele_selected = null;
      this.editor_selected = false;

      //End move
      this.movingX = 0;
      this.movingY = 0;
      this.timestampX = 0;
      this.timestampY = 0;
    }
  }, {
    key: "contextmenu",
    value: function contextmenu(e) {
      e.preventDefault();

      if (this.editor_mode === 'fixed') {
        return false;
      }

      if (this.precanvas.getElementsByClassName("drawflow-delete").length) {
        //this.precanvas.getElementsByClassName("drawflow-delete")[0].remove();
        this.precanvas.getElementsByClassName("drawflow-delete")[0].parentNode.removeChild(this.precanvas.getElementsByClassName("drawflow-delete")[0])
      }

      ;

      if (this.node_selected || this.connection_selected) {
        var deletebox = document.createElement('div');
        $(deletebox).addClass("drawflow-delete");
        deletebox.innerHTML = "x";

        if (this.node_selected) {
          this.node_selected.appendChild(deletebox);
        }

        if (this.connection_selected) {
          deletebox.style.top = e.clientY * (this.precanvas.clientHeight / (this.precanvas.clientHeight * this.zoom)) - this.precanvas.getBoundingClientRect().top * (this.precanvas.clientHeight / (this.precanvas.clientHeight * this.zoom)) + "px";
          deletebox.style.left = e.clientX * (this.precanvas.clientWidth / (this.precanvas.clientWidth * this.zoom)) - this.precanvas.getBoundingClientRect().left * (this.precanvas.clientWidth / (this.precanvas.clientWidth * this.zoom)) + "px";
          this.precanvas.appendChild(deletebox);
        }
      }
    }
  }, {
    key: "key",
    value: function key(e) {
      if (this.editor_mode === 'fixed') {
        return false;
      }

      if (e.key === 'Del' || e.key === 'Delete' || e.key === 'Backspace' && e.metaKey) {
        if (this.node_selected != null) {
          if (this.first_click.tagName !== 'INPUT' && this.first_click.tagName !== 'TEXTAREA' && this.first_click.hasAttribute('contenteditable') !== true) {
            this.removeNodeId(this.node_selected.id);
          }
        }

        if (this.connection_selected != null) {
          this.removeConnection();
        }
      }
    }
  }, {
    key: "zoom_enter",
    value: function zoom_enter(event, delta) {
      if (event.ctrlKey) {
        event.preventDefault();

        if (event.deltaY > 0) {
          // Zoom Out
          this.zoom_out('zoom_out');
        } else {
          // Zoom In
          this.zoom_in('zoom_in');
        } //this.precanvas.style.transform = "translate("+this.canvas_x+"px, "+this.canvas_y+"px) scale("+this.zoom+")";
      }
    }
  }, {
    key: "zoom_refresh",
    value: function zoom_refresh(zoom) {
      switch (zoom) {
        case 'zoom_out':
        this.canvas_x += 10;
        this.canvas_y += 10;
          break;
        case 'zoom_in':
          this.canvas_x -= 10;
          this.canvas_y -= 10;
          break;
        default:
          this.canvas_x = 0;
          this.canvas_y = 0;
          break;
      }
      this.dispatch('zoom', this.zoom);
      this.precanvas.style.transform = "translate(" + this.canvas_x + "px, " + this.canvas_y + "px) scale(" + this.zoom + ")";
      this.update_container_size(null, true);
    }
  }, {
    key: "zoom_in",
    value: function zoom_in() {
      if (this.zoom < this.zoom_max) {
        this.zoom += 0.1;
        this.zoom_refresh('zoom_in');
      }
    }
  }, {
    key: "zoom_out",
    value: function zoom_out() {
      if (this.zoom > this.zoom_min) {
        this.zoom -= 0.1;
        this.zoom_refresh('zoom_out');
      }
    }
  }, {
    key: "zoom_reset",
    value: function zoom_reset() {
      if (this.zoom != 1) {
        this.zoom = 1;
        this.zoom_refresh();
      }
    }
  }, {
    key: "createCurvature",
    value: function createCurvature(start_pos_x, start_pos_y, end_pos_x, end_pos_y, curvature_value, type) {
      var line_x = start_pos_x;
      var line_y = start_pos_y;
      var x = end_pos_x;
      var y = end_pos_y;
      var curvature = curvature_value; //type openclose open close other

      switch (type) {
        case 'open':
          if (start_pos_x >= end_pos_x) {
            var hx1 = line_x + Math.abs(x - line_x) * curvature;
            var hx2 = x - Math.abs(x - line_x) * (curvature * -1);
          } else {
            var hx1 = line_x + Math.abs(x - line_x) * curvature;
            var hx2 = x - Math.abs(x - line_x) * curvature;
          }

          return ' M ' + line_x + ' ' + line_y + ' C ' + hx1 + ' ' + line_y + ' ' + hx2 + ' ' + y + ' ' + x + '  ' + y;
          break;

        case 'close':
          if (start_pos_x >= end_pos_x) {
            var hx1 = line_x + Math.abs(x - line_x) * (curvature * -1);
            var hx2 = x - Math.abs(x - line_x) * curvature;
          } else {
            var hx1 = line_x + Math.abs(x - line_x) * curvature;
            var hx2 = x - Math.abs(x - line_x) * curvature;
          }

          return ' M ' + line_x + ' ' + line_y + ' C ' + hx1 + ' ' + line_y + ' ' + hx2 + ' ' + y + ' ' + x + '  ' + y;
          break;

        case 'other':
          if (start_pos_x >= end_pos_x) {
            var hx1 = line_x + Math.abs(x - line_x) * (curvature * -1);
            var hx2 = x - Math.abs(x - line_x) * (curvature * -1);
          } else {
            var hx1 = line_x + Math.abs(x - line_x) * curvature;
            var hx2 = x - Math.abs(x - line_x) * curvature;
          }

          return ' M ' + line_x + ' ' + line_y + ' C ' + hx1 + ' ' + line_y + ' ' + hx2 + ' ' + y + ' ' + x + '  ' + y;
          break;

        default:
          var hx1 = line_x + Math.abs(x - line_x) * curvature;
          var hx2 = x - Math.abs(x - line_x) * curvature;
          return ' M ' + line_x + ' ' + line_y + ' C ' + hx1 + ' ' + line_y + ' ' + hx2 + ' ' + y + ' ' + x + '  ' + y;
      }
    }
  }, {
    key: "drawConnection",
    value: function drawConnection(ele) {
      var connection = document.createElementNS('http://www.w3.org/2000/svg', "svg");
      this.connection_ele = connection;
      var path = document.createElementNS('http://www.w3.org/2000/svg', "path");
      path.setAttribute('class', 'main-path');
      path.setAttributeNS(null, 'd', ''); // path.innerHTML = 'a';

      //connection.classList.add("connection");
      connection.setAttribute('class', 'connection');
      connection.appendChild(path);
      this.precanvas.appendChild(connection);
    }
  }, {
    key: "updateConnection",
    value: function updateConnection(eX, eY) {
      //var path = this.connection_ele.children[0];
      var path = this.connection_ele.firstElementChild;
      var line_x = this.ele_selected.offsetWidth / 2 + this.line_path / 2 + this.ele_selected.parentElement.parentElement.offsetLeft + this.ele_selected.offsetLeft;
      var line_y = this.ele_selected.offsetHeight / 2 + this.line_path / 2 + this.ele_selected.parentElement.parentElement.offsetTop + this.ele_selected.offsetTop;
      // var x = eX * (this.precanvas.clientWidth / (this.precanvas.clientWidth * this.zoom)) - this.precanvas.getBoundingClientRect().x * (this.precanvas.clientWidth / (this.precanvas.clientWidth * this.zoom));
      // var y = eY * (this.precanvas.clientHeight / (this.precanvas.clientHeight * this.zoom)) - this.precanvas.getBoundingClientRect().y * (this.precanvas.clientHeight / (this.precanvas.clientHeight * this.zoom));
      var x = eX * (this.precanvas.clientWidth / (this.precanvas.clientWidth * this.zoom)) - this.precanvas.getBoundingClientRect().left * (this.precanvas.clientWidth / (this.precanvas.clientWidth * this.zoom));
      var y = eY * (this.precanvas.clientHeight / (this.precanvas.clientHeight * this.zoom)) - this.precanvas.getBoundingClientRect().top * (this.precanvas.clientHeight / (this.precanvas.clientHeight * this.zoom));
      /*
      var curvature = 0.5;
      var hx1 = line_x + Math.abs(x - line_x) * curvature;
      var hx2 = x - Math.abs(x - line_x) * curvature;
      */
      //path.setAttributeNS(null, 'd', 'M '+ line_x +' '+ line_y +' C '+ hx1 +' '+ line_y +' '+ hx2 +' ' + y +' ' + x +'  ' + y);

      var curvature = this.curvature;
      var lineCurve = this.createCurvature(line_x, line_y, x, y, curvature, 'openclose');
      path.setAttributeNS(null, 'd', lineCurve);
    }
    // }, {
    //   key: "addConnection",
    //   value: function addConnection(id_output, id_input, output_class, input_class) {
    //     var nodeOneModule = this.getModuleFromNodeId(id_output);
    //     var nodeTwoModule = this.getModuleFromNodeId(id_input);

    //     if (nodeOneModule === nodeTwoModule) {
    //       var dataNode = this.getNodeFromId(id_output);
    //       var exist = false;

    //       for (var checkOutput in dataNode.outputs[output_class].connections) {
    //         var connectionSearch = dataNode.outputs[output_class].connections[checkOutput];

    //         if (connectionSearch.node == id_input && connectionSearch.output == input_class) {
    //           exist = true;
    //         }
    //       } // Check connection exist


    //       if (exist === false) {
    //         //Create Connection
    //         this.drawflow.drawflow[nodeOneModule].data[id_output].outputs[output_class].connections.push({
    //           "node": id_input,
    //           "output": input_class
    //         });
    //         this.drawflow.drawflow[nodeOneModule].data[id_input].inputs[input_class].connections.push({
    //           "node": id_output,
    //           "input": output_class
    //         });

    //         if (this.module === nodeOneModule) {
    //           //Draw connection
    //           var connection = document.createElementNS('http://www.w3.org/2000/svg', "svg");
    //           var path = document.createElementNS('http://www.w3.org/2000/svg', "path");
    //           path.classList.add("main-path");
    //           path.setAttributeNS(null, 'd', ''); // path.innerHTML = 'a';

    //           connection.classList.add("connection");
    //           connection.classList.add("node_in_node-" + id_input);
    //           connection.classList.add("node_out_node-" + id_output);
    //           connection.classList.add(output_class);
    //           connection.classList.add(input_class);
    //           connection.appendChild(path);
    //           this.precanvas.appendChild(connection);
    //           this.updateConnectionNodes('node-' + id_output);
    //           this.updateConnectionNodes('node-' + id_input);
    //         }

    //         this.dispatch('connectionCreated', {
    //           output_id: id_output,
    //           input_id: id_input,
    //           output_class: output_class,
    //           input_class: input_class
    //         });
    //       }
    //     }
    //   }
  }, {
    key: "updateConnectionNodes",
    value: function updateConnectionNodes(id) {
      // Aquí nos quedamos;
      var idSearch = 'node_in_' + id;
      var idSearchOut = 'node_out_' + id;
      var line_path = this.line_path / 2;
      var precanvas = this.precanvas;
      var curvature = this.curvature;
      var createCurvature = this.createCurvature;
      var reroute_curvature = this.reroute_curvature;
      var reroute_curvature_start_end = this.reroute_curvature_start_end;
      var reroute_fix_curvature = this.reroute_fix_curvature;
      var rerouteWidth = this.reroute_width;
      var zoom = this.zoom;
      var elemsOut = document.getElementsByClassName(idSearchOut);
      Object.keys(elemsOut).map(function (item, index) {
        if (elemsOut[item].querySelector('.point') === null) {
          var elemtsearchId_out = document.getElementById(id);
          var classList = elemsOut[item].getAttribute('class').split(" ");
          var id_search = classList[1].replace('node_in_', '');
          var elemtsearchId = document.getElementById(id_search);
          var elemtsearch = elemtsearchId.querySelectorAll('.' + classList[4])[0];
          var eX = elemtsearch.offsetWidth / 2 + line_path + elemtsearch.parentElement.parentElement.offsetLeft + elemtsearch.offsetLeft;
          var eY = elemtsearch.offsetHeight / 2 + line_path + elemtsearch.parentElement.parentElement.offsetTop + elemtsearch.offsetTop;
          var line_x = elemtsearchId_out.offsetLeft + elemtsearchId_out.querySelectorAll('.' + classList[3])[0].offsetLeft + elemtsearchId_out.querySelectorAll('.' + classList[3])[0].offsetWidth / 2 + line_path;
          var line_y = elemtsearchId_out.offsetTop + elemtsearchId_out.querySelectorAll('.' + classList[3])[0].offsetTop + elemtsearchId_out.querySelectorAll('.' + classList[3])[0].offsetHeight / 2 + line_path;
          var x = eX;
          var y = eY;
          /*
          var curvature = 0.5;
          var hx1 = line_x + Math.abs(x - line_x) * curvature;
          var hx2 = x - Math.abs(x - line_x) * curvature;
          // console.log('M '+ line_x +' '+ line_y +' C '+ hx1 +' '+ line_y +' '+ hx2 +' ' + y +' ' + x +'  ' + y );
          elemsOut[item].children[0].setAttributeNS(null, 'd', 'M '+ line_x +' '+ line_y +' C '+ hx1 +' '+ line_y +' '+ hx2 +' ' + y +' ' + x +'  ' + y );
          */

          var lineCurve = createCurvature(line_x, line_y, x, y, curvature, 'openclose');
          elemsOut[item].firstElementChild.setAttributeNS(null, 'd', lineCurve);
        } else {
          var points = elemsOut[item].querySelectorAll('.point');
          var linecurve = '';
          var reoute_fix = [];
          points.forEach(function (item, i) {
            if (i === 0 && points.length - 1 === 0) {
              // M line_x line_y C hx1 line_y hx2 y x y
              var elemtsearchId_out = document.getElementById(id);
              var elemtsearch = item;
              var eX = (elemtsearch.getBoundingClientRect().x - precanvas.getBoundingClientRect().x) * (precanvas.clientWidth / (precanvas.clientWidth * zoom)) + rerouteWidth;
              var eY = (elemtsearch.getBoundingClientRect().y - precanvas.getBoundingClientRect().y) * (precanvas.clientHeight / (precanvas.clientHeight * zoom)) + rerouteWidth;
              var line_x = elemtsearchId_out.offsetLeft + elemtsearchId_out.querySelectorAll('.' + item.parentElement.classList[3])[0].offsetLeft + elemtsearchId_out.querySelectorAll('.' + item.parentElement.classList[3])[0].offsetWidth / 2 + line_path;
              var line_y = elemtsearchId_out.offsetTop + elemtsearchId_out.querySelectorAll('.' + item.parentElement.classList[3])[0].offsetTop + elemtsearchId_out.querySelectorAll('.' + item.parentElement.classList[3])[0].offsetHeight / 2 + line_path;
              var x = eX;
              var y = eY;
              /*var curvature = 0.5;
              var hx1 = line_x + Math.abs(x - line_x) * curvature;
              var hx2 = x - Math.abs(x - line_x) * curvature;
              linecurve += ' M '+ line_x +' '+ line_y +' C '+ hx1 +' '+ line_y +' '+ hx2 +' ' + y +' ' + x +'  ' + y;*/

              var lineCurveSearch = createCurvature(line_x, line_y, x, y, reroute_curvature_start_end, 'open');
              linecurve += lineCurveSearch;
              reoute_fix.push(lineCurveSearch); //var elemtsearchId_out = document.getElementById(id);

              var elemtsearchId_out = item;
              var id_search = item.parentElement.classList[1].replace('node_in_', '');
              var elemtsearchId = document.getElementById(id_search);
              var elemtsearch = elemtsearchId.querySelectorAll('.' + item.parentElement.classList[4])[0];
              var eX = elemtsearch.offsetWidth / 2 + line_path + elemtsearch.parentElement.parentElement.offsetLeft + elemtsearch.offsetLeft;
              var eY = elemtsearch.offsetHeight / 2 + line_path + elemtsearch.parentElement.parentElement.offsetTop + elemtsearch.offsetTop;
              var line_x = (elemtsearchId_out.getBoundingClientRect().x - precanvas.getBoundingClientRect().x) * (precanvas.clientWidth / (precanvas.clientWidth * zoom)) + rerouteWidth;
              var line_y = (elemtsearchId_out.getBoundingClientRect().y - precanvas.getBoundingClientRect().y) * (precanvas.clientHeight / (precanvas.clientHeight * zoom)) + rerouteWidth;
              var x = eX;
              var y = eY;
              /*
              var curvature = 0.5;
              var hx1 = line_x + Math.abs(x - line_x) * curvature;
              var hx2 = x - Math.abs(x - line_x) * curvature;
              linecurve += ' M '+ line_x +' '+ line_y +' C '+ hx1 +' '+ line_y +' '+ hx2 +' ' + y +' ' + x +'  ' + y;
              */

              var lineCurveSearch = createCurvature(line_x, line_y, x, y, reroute_curvature_start_end, 'close');
              linecurve += lineCurveSearch;
              reoute_fix.push(lineCurveSearch);
            } else if (i === 0) {
              //console.log("Primero");
              // M line_x line_y C hx1 line_y hx2 y x y
              // FIRST
              var elemtsearchId_out = document.getElementById(id);
              var elemtsearch = item;
              var eX = (elemtsearch.getBoundingClientRect().x - precanvas.getBoundingClientRect().x) * (precanvas.clientWidth / (precanvas.clientWidth * zoom)) + rerouteWidth;
              var eY = (elemtsearch.getBoundingClientRect().y - precanvas.getBoundingClientRect().y) * (precanvas.clientHeight / (precanvas.clientHeight * zoom)) + rerouteWidth;
              var line_x = elemtsearchId_out.offsetLeft + elemtsearchId_out.querySelectorAll('.' + item.parentElement.classList[3])[0].offsetLeft + elemtsearchId_out.querySelectorAll('.' + item.parentElement.classList[3])[0].offsetWidth / 2 + line_path;
              var line_y = elemtsearchId_out.offsetTop + elemtsearchId_out.querySelectorAll('.' + item.parentElement.classList[3])[0].offsetTop + elemtsearchId_out.querySelectorAll('.' + item.parentElement.classList[3])[0].offsetHeight / 2 + line_path;
              var x = eX;
              var y = eY;
              /*
              var curvature = 0.5;
              var hx1 = line_x + Math.abs(x - line_x) * curvature;
              var hx2 = x - Math.abs(x - line_x) * curvature;
              linecurve += ' M '+ line_x +' '+ line_y +' C '+ hx1 +' '+ line_y +' '+ hx2 +' ' + y +' ' + x +'  ' + y;*/

              var lineCurveSearch = createCurvature(line_x, line_y, x, y, reroute_curvature_start_end, 'open');
              linecurve += lineCurveSearch;
              reoute_fix.push(lineCurveSearch); // SECOND

              var elemtsearchId_out = item;
              var elemtsearch = points[i + 1];
              var eX = (elemtsearch.getBoundingClientRect().x - precanvas.getBoundingClientRect().x) * (precanvas.clientWidth / (precanvas.clientWidth * zoom)) + rerouteWidth;
              var eY = (elemtsearch.getBoundingClientRect().y - precanvas.getBoundingClientRect().y) * (precanvas.clientHeight / (precanvas.clientHeight * zoom)) + rerouteWidth;
              var line_x = (elemtsearchId_out.getBoundingClientRect().x - precanvas.getBoundingClientRect().x) * (precanvas.clientWidth / (precanvas.clientWidth * zoom)) + rerouteWidth;
              var line_y = (elemtsearchId_out.getBoundingClientRect().y - precanvas.getBoundingClientRect().y) * (precanvas.clientHeight / (precanvas.clientHeight * zoom)) + rerouteWidth;
              var x = eX;
              var y = eY;
              /*
              var curvature = reroute_curvature;
              var hx1 = line_x + Math.abs(x - line_x) * curvature;
              var hx2 = x - Math.abs(x - line_x) * curvature;
              linecurve += ' M '+ line_x +' '+ line_y +' C '+ hx1 +' '+ line_y +' '+ hx2 +' ' + y +' ' + x +'  ' + y;*/

              var lineCurveSearch = createCurvature(line_x, line_y, x, y, reroute_curvature, 'other');
              linecurve += lineCurveSearch;
              reoute_fix.push(lineCurveSearch);
            } else if (i === points.length - 1) {
              //console.log("Final");
              var elemtsearchId_out = item;
              var id_search = item.parentElement.classList[1].replace('node_in_', '');
              var elemtsearchId = document.getElementById(id_search);
              var elemtsearch = elemtsearchId.querySelectorAll('.' + item.parentElement.classList[4])[0];
              var eX = elemtsearch.offsetWidth / 2 + line_path + elemtsearch.parentElement.parentElement.offsetLeft + elemtsearch.offsetLeft;
              var eY = elemtsearch.offsetHeight / 2 + line_path + elemtsearch.parentElement.parentElement.offsetTop + elemtsearch.offsetTop;
              var line_x = (elemtsearchId_out.getBoundingClientRect().x - precanvas.getBoundingClientRect().x) * (precanvas.clientWidth / (precanvas.clientWidth * zoom)) + rerouteWidth;
              var line_y = (elemtsearchId_out.getBoundingClientRect().y - precanvas.getBoundingClientRect().y) * (precanvas.clientHeight / (precanvas.clientHeight * zoom)) + rerouteWidth;
              var x = eX;
              var y = eY;
              /*
              var curvature = 0.5;
              var hx1 = line_x + Math.abs(x - line_x) * curvature;
              var hx2 = x - Math.abs(x - line_x) * curvature;
              linecurve += ' M '+ line_x +' '+ line_y +' C '+ hx1 +' '+ line_y +' '+ hx2 +' ' + y +' ' + x +'  ' + y;*/

              var lineCurveSearch = createCurvature(line_x, line_y, x, y, reroute_curvature_start_end, 'close');
              linecurve += lineCurveSearch;
              reoute_fix.push(lineCurveSearch);
            } else {
              var elemtsearchId_out = item;
              var elemtsearch = points[i + 1];
              var eX = (elemtsearch.getBoundingClientRect().x - precanvas.getBoundingClientRect().x) * (precanvas.clientWidth / (precanvas.clientWidth * zoom)) + rerouteWidth;
              var eY = (elemtsearch.getBoundingClientRect().y - precanvas.getBoundingClientRect().y) * (precanvas.clientHeight / (precanvas.clientHeight * zoom)) + rerouteWidth;
              var line_x = (elemtsearchId_out.getBoundingClientRect().x - precanvas.getBoundingClientRect().x) * (precanvas.clientWidth / (precanvas.clientWidth * zoom)) + rerouteWidth;
              var line_y = (elemtsearchId_out.getBoundingClientRect().y - precanvas.getBoundingClientRect().y) * (precanvas.clientHeight / (precanvas.clientHeight * zoom)) + rerouteWidth;
              var x = eX;
              var y = eY;
              /*
              var curvature = reroute_curvature;
              var hx1 = line_x + Math.abs(x - line_x) * curvature;
              var hx2 = x - Math.abs(x - line_x) * curvature;
              linecurve += ' M '+ line_x +' '+ line_y +' C '+ hx1 +' '+ line_y +' '+ hx2 +' ' + y +' ' + x +'  ' + y;*/

              var lineCurveSearch = createCurvature(line_x, line_y, x, y, reroute_curvature, 'other');
              linecurve += lineCurveSearch;
              reoute_fix.push(lineCurveSearch);
            }
          });

          if (reroute_fix_curvature) {
            reoute_fix.forEach(function (itempath, i) {
              elemsOut[item].children[i].setAttributeNS(null, 'd', itempath);
            });
          } else {
            elemsOut[item].firstElementChild.setAttributeNS(null, 'd', linecurve);
          }
        }
      });
      var elems = document.getElementsByClassName(idSearch);
      Object.keys(elems).map(function (item, index) {
        // console.log("In")
        if (elems[item].querySelector('.point') === null) {
          var elemtsearchId_in = document.getElementById(id);
          var classList = elems[item].getAttribute('class').split(" ");
          var id_search = classList[2].replace('node_out_', '');
          var elemtsearchId = document.getElementById(id_search);
          var elemtsearch = elemtsearchId.querySelectorAll('.' + classList[3])[0];
          var line_x = elemtsearch.offsetWidth / 2 + line_path + elemtsearch.parentElement.parentElement.offsetLeft + elemtsearch.offsetLeft;
          var line_y = elemtsearch.offsetHeight / 2 + line_path + elemtsearch.parentElement.parentElement.offsetTop + elemtsearch.offsetTop;
          var x = elemtsearchId_in.offsetLeft + elemtsearchId_in.querySelectorAll('.' + classList[4])[0].offsetLeft + elemtsearchId_in.querySelectorAll('.' + classList[4])[0].offsetWidth / 2 + line_path;
          var y = elemtsearchId_in.offsetTop + elemtsearchId_in.querySelectorAll('.' + classList[4])[0].offsetTop + elemtsearchId_in.querySelectorAll('.' + classList[4])[0].offsetHeight / 2 + line_path;
          /*
          var curvature = 0.5;
          var hx1 = line_x + Math.abs(x - line_x) * curvature;
          var hx2 = x - Math.abs(x - line_x) * curvature;
          // console.log('M '+ line_x +' '+ line_y +' C '+ hx1 +' '+ line_y +' '+ hx2 +' ' + y +' ' + x +'  ' + y );
          elems[item].children[0].setAttributeNS(null, 'd', 'M '+ line_x +' '+ line_y +' C '+ hx1 +' '+ line_y +' '+ hx2 +' ' + y +' ' + x +'  ' + y );*/

          var lineCurve = createCurvature(line_x, line_y, x, y, curvature, 'openclose');
          elems[item].firstElementChild.setAttributeNS(null, 'd', lineCurve);
        } else {
          var points = elems[item].querySelectorAll('.point');
          var linecurve = '';
          var reoute_fix = [];
          points.forEach(function (item, i) {
            if (i === 0 && points.length - 1 === 0) {
              // M line_x line_y C hx1 line_y hx2 y x y
              var elemtsearchId_out = document.getElementById(id);
              var elemtsearch = item;
              var line_x = (elemtsearch.getBoundingClientRect().x - precanvas.getBoundingClientRect().x) * (precanvas.clientWidth / (precanvas.clientWidth * zoom)) + rerouteWidth;
              var line_y = (elemtsearch.getBoundingClientRect().y - precanvas.getBoundingClientRect().y) * (precanvas.clientHeight / (precanvas.clientHeight * zoom)) + rerouteWidth;
              var eX = elemtsearchId_out.offsetLeft + elemtsearchId_out.querySelectorAll('.' + item.parentElement.classList[4])[0].offsetLeft + elemtsearchId_out.querySelectorAll('.' + item.parentElement.classList[4])[0].offsetWidth / 2 + line_path;
              var eY = elemtsearchId_out.offsetTop + elemtsearchId_out.querySelectorAll('.' + item.parentElement.classList[4])[0].offsetTop + elemtsearchId_out.querySelectorAll('.' + item.parentElement.classList[4])[0].offsetHeight / 2 + line_path;
              var x = eX;
              var y = eY;
              /*
              var curvature = 0.5;
              var hx1 = line_x + Math.abs(x - line_x) * curvature;
              var hx2 = x - Math.abs(x - line_x) * curvature;
              linecurve += ' M '+ line_x +' '+ line_y +' C '+ hx1 +' '+ line_y +' '+ hx2 +' ' + y +' ' + x +'  ' + y;*/

              var lineCurveSearch = createCurvature(line_x, line_y, x, y, reroute_curvature_start_end, 'close');
              linecurve += lineCurveSearch;
              reoute_fix.push(lineCurveSearch); //var elemtsearchId_out = document.getElementById(id);

              var elemtsearchId_out = item;
              var id_search = item.parentElement.classList[2].replace('node_out_', '');
              var elemtsearchId = document.getElementById(id_search);
              var elemtsearch = elemtsearchId.querySelectorAll('.' + item.parentElement.classList[3])[0];
              var line_x = elemtsearch.offsetWidth / 2 + line_path + elemtsearch.parentElement.parentElement.offsetLeft + elemtsearch.offsetLeft;
              var line_y = elemtsearch.offsetHeight / 2 + line_path + elemtsearch.parentElement.parentElement.offsetTop + elemtsearch.offsetTop;
              var eX = (elemtsearchId_out.getBoundingClientRect().x - precanvas.getBoundingClientRect().x) * (precanvas.clientWidth / (precanvas.clientWidth * zoom)) + rerouteWidth;
              var eY = (elemtsearchId_out.getBoundingClientRect().y - precanvas.getBoundingClientRect().y) * (precanvas.clientHeight / (precanvas.clientHeight * zoom)) + rerouteWidth;
              var x = eX;
              var y = eY;
              /*
              var curvature = 0.5;
              var hx1 = line_x + Math.abs(x - line_x) * curvature;
              var hx2 = x - Math.abs(x - line_x) * curvature;
              linecurve += ' M '+ line_x +' '+ line_y +' C '+ hx1 +' '+ line_y +' '+ hx2 +' ' + y +' ' + x +'  ' + y;*/

              var lineCurveSearch = createCurvature(line_x, line_y, x, y, reroute_curvature_start_end, 'open');
              linecurve += lineCurveSearch;
              reoute_fix.push(lineCurveSearch);
            } else if (i === 0) {
              // M line_x line_y C hx1 line_y hx2 y x y
              // FIRST
              var elemtsearchId_out = item;
              var id_search = item.parentElement.classList[2].replace('node_out_', '');
              var elemtsearchId = document.getElementById(id_search);
              var elemtsearch = elemtsearchId.querySelectorAll('.' + item.parentElement.classList[3])[0];
              var line_x = elemtsearch.offsetWidth / 2 + line_path + elemtsearch.parentElement.parentElement.offsetLeft + elemtsearch.offsetLeft;
              var line_y = elemtsearch.offsetHeight / 2 + line_path + elemtsearch.parentElement.parentElement.offsetTop + elemtsearch.offsetTop;
              var eX = (elemtsearchId_out.getBoundingClientRect().x - precanvas.getBoundingClientRect().x) * (precanvas.clientWidth / (precanvas.clientWidth * zoom)) + rerouteWidth;
              var eY = (elemtsearchId_out.getBoundingClientRect().y - precanvas.getBoundingClientRect().y) * (precanvas.clientHeight / (precanvas.clientHeight * zoom)) + rerouteWidth;
              var x = eX;
              var y = eY;
              /*
              var curvature = 0.5;
              var hx1 = line_x + Math.abs(x - line_x) * curvature;
              var hx2 = x - Math.abs(x - line_x) * curvature;
              linecurve += ' M '+ line_x +' '+ line_y +' C '+ hx1 +' '+ line_y +' '+ hx2 +' ' + y +' ' + x +'  ' + y;*/

              var lineCurveSearch = createCurvature(line_x, line_y, x, y, reroute_curvature_start_end, 'open');
              linecurve += lineCurveSearch;
              reoute_fix.push(lineCurveSearch); // SECOND

              var elemtsearchId_out = item;
              var elemtsearch = points[i + 1];
              var eX = (elemtsearch.getBoundingClientRect().x - precanvas.getBoundingClientRect().x) * (precanvas.clientWidth / (precanvas.clientWidth * zoom)) + rerouteWidth;
              var eY = (elemtsearch.getBoundingClientRect().y - precanvas.getBoundingClientRect().y) * (precanvas.clientHeight / (precanvas.clientHeight * zoom)) + rerouteWidth;
              var line_x = (elemtsearchId_out.getBoundingClientRect().x - precanvas.getBoundingClientRect().x) * (precanvas.clientWidth / (precanvas.clientWidth * zoom)) + rerouteWidth;
              var line_y = (elemtsearchId_out.getBoundingClientRect().y - precanvas.getBoundingClientRect().y) * (precanvas.clientHeight / (precanvas.clientHeight * zoom)) + rerouteWidth;
              var x = eX;
              var y = eY;
              /*
              var curvature = reroute_curvature;
              var hx1 = line_x + Math.abs(x - line_x) * curvature;
              var hx2 = x - Math.abs(x - line_x) * curvature;
              linecurve += ' M '+ line_x +' '+ line_y +' C '+ hx1 +' '+ line_y +' '+ hx2 +' ' + y +' ' + x +'  ' + y;*/

              var lineCurveSearch = createCurvature(line_x, line_y, x, y, reroute_curvature, 'other');
              linecurve += lineCurveSearch;
              reoute_fix.push(lineCurveSearch);
            } else if (i === points.length - 1) {
              var elemtsearchId_out = item;
              var id_search = item.parentElement.classList[1].replace('node_in_', '');
              var elemtsearchId = document.getElementById(id_search);
              var elemtsearch = elemtsearchId.querySelectorAll('.' + item.parentElement.classList[4])[0];
              var eX = elemtsearch.offsetWidth / 2 + line_path + elemtsearch.parentElement.parentElement.offsetLeft + elemtsearch.offsetLeft;
              var eY = elemtsearch.offsetHeight / 2 + line_path + elemtsearch.parentElement.parentElement.offsetTop + elemtsearch.offsetTop;
              var line_x = (elemtsearchId_out.getBoundingClientRect().x - precanvas.getBoundingClientRect().x) * (precanvas.clientWidth / (precanvas.clientWidth * zoom)) + rerouteWidth;
              var line_y = (elemtsearchId_out.getBoundingClientRect().y - precanvas.getBoundingClientRect().y) * (precanvas.clientHeight / (precanvas.clientHeight * zoom)) + rerouteWidth;
              var x = eX;
              var y = eY;
              /*
              var curvature = 0.5;
              var hx1 = line_x + Math.abs(x - line_x) * curvature;
              var hx2 = x - Math.abs(x - line_x) * curvature;
              linecurve += ' M '+ line_x +' '+ line_y +' C '+ hx1 +' '+ line_y +' '+ hx2 +' ' + y +' ' + x +'  ' + y;*/

              var lineCurveSearch = createCurvature(line_x, line_y, x, y, reroute_curvature_start_end, 'close');
              linecurve += lineCurveSearch;
              reoute_fix.push(lineCurveSearch);
            } else {
              var elemtsearchId_out = item;
              var elemtsearch = points[i + 1];
              var eX = (elemtsearch.getBoundingClientRect().x - precanvas.getBoundingClientRect().x) * (precanvas.clientWidth / (precanvas.clientWidth * zoom)) + rerouteWidth;
              var eY = (elemtsearch.getBoundingClientRect().y - precanvas.getBoundingClientRect().y) * (precanvas.clientHeight / (precanvas.clientHeight * zoom)) + rerouteWidth;
              var line_x = (elemtsearchId_out.getBoundingClientRect().x - precanvas.getBoundingClientRect().x) * (precanvas.clientWidth / (precanvas.clientWidth * zoom)) + rerouteWidth;
              var line_y = (elemtsearchId_out.getBoundingClientRect().y - precanvas.getBoundingClientRect().y) * (precanvas.clientHeight / (precanvas.clientHeight * zoom)) + rerouteWidth;
              var x = eX;
              var y = eY;
              /*
              var curvature = reroute_curvature;
              var hx1 = line_x + Math.abs(x - line_x) * curvature;
              var hx2 = x - Math.abs(x - line_x) * curvature;
              linecurve += ' M '+ line_x +' '+ line_y +' C '+ hx1 +' '+ line_y +' '+ hx2 +' ' + y +' ' + x +'  ' + y;
              */

              var lineCurveSearch = createCurvature(line_x, line_y, x, y, reroute_curvature, 'other');
              linecurve += lineCurveSearch;
              reoute_fix.push(lineCurveSearch);
            }
          });

          if (reroute_fix_curvature) {
            reoute_fix.forEach(function (itempath, i) {
              elems[item].children[i].setAttributeNS(null, 'd', itempath);
            });
          } else {
            elems[item].children[0].setAttributeNS(null, 'd', linecurve);
          }
        }
      });
    }
    // }, {
    //   key: "dblclick",
    //   value: function dblclick(e) {
    //     if (this.connection_selected != null && this.reroute) {
    //       this.createReroutePoint(this.connection_selected);
    //     }

    //     if (e.target.classList[0] === 'point') {
    //       this.removeReroutePoint(e.target);
    //     }
    //   }
  }, {
    key: "createReroutePoint",
    value: function createReroutePoint(ele) {
      this.connection_selected.classList.remove("selected");
      var nodeUpdate = this.connection_selected.parentElement.classList[2].slice(9);
      var nodeUpdateIn = this.connection_selected.parentElement.classList[1].slice(13);
      var output_class = this.connection_selected.parentElement.classList[3];
      var input_class = this.connection_selected.parentElement.classList[4];
      this.connection_selected = null;
      var point = document.createElementNS('http://www.w3.org/2000/svg', "circle");
      point.classList.add("point");
      var pos_x = this.pos_x * (this.precanvas.clientWidth / (this.precanvas.clientWidth * this.zoom)) - this.precanvas.getBoundingClientRect().x * (this.precanvas.clientWidth / (this.precanvas.clientWidth * this.zoom));
      var pos_y = this.pos_y * (this.precanvas.clientHeight / (this.precanvas.clientHeight * this.zoom)) - this.precanvas.getBoundingClientRect().y * (this.precanvas.clientHeight / (this.precanvas.clientHeight * this.zoom));
      point.setAttributeNS(null, 'cx', pos_x);
      point.setAttributeNS(null, 'cy', pos_y);
      point.setAttributeNS(null, 'r', this.reroute_width);
      var position_add_array_point = 0;

      if (this.reroute_fix_curvature) {
        var numberPoints = ele.parentElement.querySelectorAll(".main-path").length;
        var path = document.createElementNS('http://www.w3.org/2000/svg', "path");
        path.classList.add("main-path");
        path.setAttributeNS(null, 'd', '');
        ele.parentElement.insertBefore(path, ele.parentElement.children[numberPoints]);

        if (numberPoints === 1) {
          ele.parentElement.appendChild(point);
        } else {
          var search_point = Array.from(ele.parentElement.children).indexOf(ele);
          position_add_array_point = search_point;
          ele.parentElement.insertBefore(point, ele.parentElement.children[search_point + numberPoints + 1]);
        }
      } else {
        ele.parentElement.appendChild(point);
      }

      var nodeId = nodeUpdate.slice(5);
      var searchConnection = this.drawflow.drawflow[this.module].data[nodeId].outputs[output_class].connections.findIndex(function (item, i) {
        return item.node === nodeUpdateIn && item.output === input_class;
      });

      if (this.drawflow.drawflow[this.module].data[nodeId].outputs[output_class].connections[searchConnection].points === undefined) {
        this.drawflow.drawflow[this.module].data[nodeId].outputs[output_class].connections[searchConnection].points = [];
      } //this.drawflow.drawflow[this.module].data[nodeId].outputs[output_class].connections[searchConnection].points.push({ pos_x: pos_x, pos_y: pos_y });


      if (this.reroute_fix_curvature) {
        //console.log(position_add_array_point)
        if (position_add_array_point > 0) {
          this.drawflow.drawflow[this.module].data[nodeId].outputs[output_class].connections[searchConnection].points.splice(position_add_array_point, 0, {
            pos_x: pos_x,
            pos_y: pos_y
          });
        } else {
          this.drawflow.drawflow[this.module].data[nodeId].outputs[output_class].connections[searchConnection].points.push({
            pos_x: pos_x,
            pos_y: pos_y
          });
        }

        ele.parentElement.querySelectorAll(".main-path").forEach(function (item, i) {
          item.classList.remove("selected");
        });
      } else {
        this.drawflow.drawflow[this.module].data[nodeId].outputs[output_class].connections[searchConnection].points.push({
          pos_x: pos_x,
          pos_y: pos_y
        });
      }
      /*
      this.drawflow.drawflow[this.module].data[nodeId].outputs[output_class].connections[searchConnection].points.sort((a,b) => (a.pos_x > b.pos_x) ? 1 : (b.pos_x > a.pos_x ) ? -1 : 0 );
      this.drawflow.drawflow[this.module].data[nodeId].outputs[output_class].connections[searchConnection].points.forEach((item, i) => {
           ele.parentElement.children[i+1].setAttributeNS(null, 'cx', item.pos_x);
          ele.parentElement.children[i+1].setAttributeNS(null, 'cy', item.pos_y);
      });*/


      this.dispatch('addReroute', nodeId);
      this.updateConnectionNodes(nodeUpdate);
    }
  }, {
    key: "removeReroutePoint",
    value: function removeReroutePoint(ele) {
      var nodeUpdate = ele.parentElement.classList[2].slice(9);
      var nodeUpdateIn = ele.parentElement.classList[1].slice(13);
      var output_class = ele.parentElement.classList[3];
      var input_class = ele.parentElement.classList[4];
      var numberPointPosition = Array.from(ele.parentElement.children).indexOf(ele) - 1;
      var nodeId = nodeUpdate.slice(5);
      var searchConnection = this.drawflow.drawflow[this.module].data[nodeId].outputs[output_class].connections.findIndex(function (item, i) {
        return item.node === nodeUpdateIn && item.output === input_class;
      });

      if (this.reroute_fix_curvature) {
        var numberMainPath = ele.parentElement.querySelectorAll(".main-path").length;
        //ele.parentElement.children[numberMainPath - 1].remove();
        ele.parentElement.removeChild(ele.parentElement.children[numberMainPath - 1]);
        numberPointPosition -= numberMainPath;

        if (numberPointPosition < 0) {
          numberPointPosition = 0;
        }
      } //console.log(numberPointPosition);


      this.drawflow.drawflow[this.module].data[nodeId].outputs[output_class].connections[searchConnection].points.splice(numberPointPosition, 1);
      //ele.remove();
      ele.parentNode.removeChild(ele);
      this.dispatch('removeReroute', nodeId);
      this.updateConnectionNodes(nodeUpdate);
    }
  }, {
    key: "registerNode",
    value: function registerNode(name, html) {
      var props = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : null;
      var options = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : null;
      this.noderegister[name] = {
        html: html,
        props: props,
        options: options
      };
    }
  }, {
    key: "getNodeFromId",
    value: function getNodeFromId(id) {
      var moduleName = this.getModuleFromNodeId(id);
      return JSON.parse(JSON.stringify(this.drawflow.drawflow[moduleName].data[id]));
    }
  }, {
    key: "getNodesFromName",
    value: function getNodesFromName(name) {
      var nodes = [];
      var editor = this.drawflow.drawflow;
      Object.keys(editor).map(function (moduleName, index) {
        for (var node in editor[moduleName].data) {
          if (editor[moduleName].data[node].name == name) {
            nodes.push(editor[moduleName].data[node].id);
          }
        }
      });
      return nodes;
    }
  }, {
    key: "addNode",
    value: function addNode(name, num_in, num_out, ele_pos_x, ele_pos_y, classoverride, data, html, typenode, id, data_res) {
      var _this = this;
      
      //var typenode = arguments.length > 8 && arguments[8] !== undefined ? arguments[8] : false;
      var parent = document.createElement('div');
      parent.classList.add("parent-node");
      var node = document.createElement('div');
      node.innerHTML = "";
      node.setAttribute("id", "node-" + id);
      node.classList.add("drawflow-node");
      node.setAttribute('data', data_res);
      if (data_res) {
        data_res = JSON.parse(data_res);
      } else {
        data_res = {};
      }
      if (classoverride != '') {
        node.classList.add(classoverride);
      }

      var inputs = document.createElement('div');
      inputs.classList.add("inputs");
      var outputs = document.createElement('div');
      outputs.classList.add("outputs");
      var json_inputs = {};

      for (var x = 0; x < num_in; x++) {
        var input = document.createElement('div');
        input.classList.add("input");
        input.classList.add("input_" + (x + 1));
        json_inputs["input_" + (x + 1)] = {
          "connections": []
        };
        inputs.appendChild(input);
      }

      var json_outputs = {};

      for (var x = 0; x < num_out; x++) {
        var output = document.createElement('div');
        output.classList.add("output");
        output.classList.add("output_" + (x + 1));
        json_outputs["output_" + (x + 1)] = {
          "connections": []
        };
        outputs.appendChild(output);
      }

      var content = document.createElement('div');
      content.classList.add("drawflow_content_node");

      if (typenode === false) {
        content.innerHTML = html;
      } else if (typenode === true) {
        content.appendChild(this.noderegister[html].html.cloneNode(true));
      } else {
        // var wrapper = new this.render(_objectSpread({
        //   render: function render(h) {
        //     return h(_this.noderegister[html].html, {
        //       props: _this.noderegister[html].props
        //     });
        //   }
        // }, this.noderegister[html].options)).$mount(); //

        // content.appendChild(wrapper.$el);
      }

      // Object.entries(data).forEach(function (key, value) {
      //   if (_typeof(key[1]) === "object") {
      //     insertObjectkeys(null, key[0], key[0]);
      //   } else {
      //     var elems = content.querySelectorAll('[df-' + key[0] + ']');

      //     for (var i = 0; i < elems.length; i++) {
      //       elems[i].value = key[1];
      //     }
      //   }
      // });

      // function insertObjectkeys(object, name, completname) {
      //   if (object === null) {
      //     var object = data[name];
      //   } else {
      //     var object = object[name];
      //   }

      //   Object.entries(object).forEach(function (key, value) {
      //     if (_typeof(key[1]) === "object") {
      //       insertObjectkeys(object, key[0], name + '-' + key[0]);
      //     } else {
      //       var elems = content.querySelectorAll('[df-' + completname + '-' + key[0] + ']');

      //       for (var i = 0; i < elems.length; i++) {
      //         elems[i].value = key[1];
      //       }
      //     }
      //   });
      // }

      node.appendChild(inputs);
      node.appendChild(content);
      node.appendChild(outputs);
      node.style.top = ele_pos_y + "px";
      node.style.left = ele_pos_x + "px";
      parent.appendChild(node);
      this.precanvas.appendChild(parent);
      var json = {
        id: id,
        name: name,
        data: data_res,
        class: classoverride,
        html: html,
        typenode: typenode,
        inputs: json_inputs,
        outputs: json_outputs,
        pos_x: ele_pos_x,
        pos_y: ele_pos_y
      };
      this.drawflow.drawflow[this.module].data[id] = json;
      this.dispatch('nodeCreated', id);
      var nodeId = this.nodeId;
      this.nodeId++;
      this.addHistory();
      return nodeId;
    }
  }, {
    key: "addNodeImport",
    value: function addNodeImport(dataNode, precanvas) {
      var _this2 = this;

      var parent = document.createElement('div');
      parent.classList.add("parent-node");
      var node = document.createElement('div');
      node.innerHTML = "";
      node.setAttribute("id", "node-" + dataNode.id);
      node.setAttribute("data", JSON.stringify(dataNode.data)),
      node.classList.add("drawflow-node");

      if (dataNode.class != '') {
        node.classList.add(dataNode.class);
      }

      var inputs = document.createElement('div');
      inputs.classList.add("inputs");
      var outputs = document.createElement('div');
      outputs.classList.add("outputs");
      Object.keys(dataNode.inputs).map(function (input_item, index) {
        var input = document.createElement('div');
        input.classList.add("input");
        input.classList.add(input_item);
        inputs.appendChild(input);
        Object.keys(dataNode.inputs[input_item].connections).map(function (output_item, index) {
          var connection = document.createElementNS('http://www.w3.org/2000/svg', "svg");
          var path = document.createElementNS('http://www.w3.org/2000/svg', "path");
          var connection_node = dataNode.id;
          var connection_input = "input_1";

          if (dataNode.inputs[input_item].connections[output_item].node != undefined) {
            connection_node = dataNode.inputs[input_item].connections[output_item].node;
          }
          if (dataNode.inputs[input_item].connections[output_item].input != undefined) {
            connection_input = dataNode.inputs[input_item].connections[output_item].input;
          }

          //path.classList.add("main-path");
          path.setAttribute("class", "main-path"),
            path.setAttributeNS(null, 'd', ''); // path.innerHTML = 'a';

          // connection.classList.add("connection");
          // connection.classList.add("node_in_node-" + dataNode.id);
          // connection.classList.add("node_out_node-" + dataNode.inputs[input_item].connections[output_item].node);
          // connection.classList.add(dataNode.inputs[input_item].connections[output_item].input);
          // connection.classList.add(input_item);
          connection.setAttribute("class", "connection" +
            " node_in_node-" + dataNode.id +
            " node_out_node-" + connection_node +
            " " + connection_input +
            " " + input_item),
            connection.appendChild(path);
          precanvas.appendChild(connection);
        });
      });

      for (var x = 0; x < Object.keys(dataNode.outputs).length; x++) {
        var output = document.createElement('div');
        output.classList.add("output");
        output.classList.add("output_" + (x + 1));
        outputs.appendChild(output);
      }

      var content = document.createElement('div');
      content.classList.add("drawflow_content_node"); //content.innerHTML = dataNode.html;

      if (dataNode.typenode === false) {
        content.innerHTML = dataNode.html;
      } else if (dataNode.typenode === true) {
        content.appendChild(this.noderegister[dataNode.html].html.cloneNode(true));
      } else {
        // var wrapper = new this.render(_objectSpread({
        //   render: function render(h) {
        //     return h(_this2.noderegister[dataNode.html].html, {
        //       props: _this2.noderegister[dataNode.html].props
        //     });
        //   }
        // }, this.noderegister[dataNode.html].options)).$mount();
        // content.appendChild(wrapper.$el);
      }

      // Object.entries(dataNode.data).forEach(function (key, value) {
      //   if (_typeof(key[1]) === "object") {
      //     insertObjectkeys(null, key[0], key[0]);
      //   } else {
      //     var elems = content.querySelectorAll('[df-' + key[0] + ']');

      //     for (var i = 0; i < elems.length; i++) {
      //       elems[i].value = key[1];
      //     }
      //   }
      // });

      // function insertObjectkeys(object, name, completname) {
      //   if (object === null) {
      //     var object = dataNode.data[name];
      //   } else {
      //     var object = object[name];
      //   }

      //   Object.entries(object).forEach(function (key, value) {
      //     if (_typeof(key[1]) === "object") {
      //       insertObjectkeys(object, key[0], name + '-' + key[0]);
      //     } else {
      //       var elems = content.querySelectorAll('[df-' + completname + '-' + key[0] + ']');

      //       for (var i = 0; i < elems.length; i++) {
      //         elems[i].value = key[1];
      //       }
      //     }
      //   });
      // }

      node.appendChild(inputs);
      node.appendChild(content);
      node.appendChild(outputs);
      node.style.top = dataNode.pos_y + "px";
      node.style.left = dataNode.pos_x + "px";
      parent.appendChild(node);
      this.precanvas.appendChild(parent);
      this.addHistory();
    }
  }, {
    key: "addRerouteImport",
    value: function addRerouteImport(dataNode) {
      var reroute_width = this.reroute_width;
      var reroute_fix_curvature = this.reroute_fix_curvature;
      Object.keys(dataNode.outputs).map(function (output_item, index) {
        Object.keys(dataNode.outputs[output_item].connections).map(function (input_item, index) {
          var points = dataNode.outputs[output_item].connections[input_item].points;

          if (points !== undefined) {
            points.forEach(function (item, i) {
              var input_id = dataNode.outputs[output_item].connections[input_item].node;
              var input_class = dataNode.outputs[output_item].connections[input_item].output; //console.log('.connection.node_in_'+input_id+'.node_out_'+dataNode.id+'.'+output_item+'.'+input_class);

              var ele = document.querySelector('.connection.node_in_node-' + input_id + '.node_out_node-' + dataNode.id + '.' + output_item + '.' + input_class);

              if (reroute_fix_curvature) {
                if (i === 0) {
                  for (var z = 0; z < points.length; z++) {
                    var path = document.createElementNS('http://www.w3.org/2000/svg', "path");
                    path.classList.add("main-path");
                    path.setAttributeNS(null, 'd', '');
                    ele.appendChild(path);
                  }
                }
              }

              var point = document.createElementNS('http://www.w3.org/2000/svg', "circle");
              point.classList.add("point");
              var pos_x = item.pos_x;
              var pos_y = item.pos_y;
              point.setAttributeNS(null, 'cx', pos_x);
              point.setAttributeNS(null, 'cy', pos_y);
              point.setAttributeNS(null, 'r', reroute_width);
              ele.appendChild(point);
            });
          }

          ;
        });
      });
      this.addHistory();
    }
  }, {
    key: "updateNodeValue",
    value: function updateNodeValue(event) {
      var attr = event.target.attributes;

      for (var i = 0; i < attr.length; i++) {
        if (attr[i].nodeName.startsWith('df-')) {
          this.drawflow.drawflow[this.module].data[event.target.closest(".drawflow_content_node").parentElement.id.slice(5)].data[attr[i].nodeName.slice(3)] = event.target.value;
        }
      }
      this.addHistory();
    }
  }, {
    key: "addNodeInput",
    value: function addNodeInput(id) {
      var moduleName = this.getModuleFromNodeId(id);
      var infoNode = this.getNodeFromId(id);
      var numInputs = Object.keys(infoNode.inputs).length;

      if (this.module === moduleName) {
        //Draw input
        var input = document.createElement('div');
        input.classList.add("input");
        input.classList.add("input_" + (numInputs + 1));
        var parent = document.querySelector('#node-' + id + ' .inputs');
        parent.appendChild(input);
        this.updateConnectionNodes('node-' + id);
      }

      this.drawflow.drawflow[moduleName].data[id].inputs["input_" + (numInputs + 1)] = {
        "connections": []
      };
      this.addHistory();
    }
  }, {
    key: "addNodeOutput",
    value: function addNodeOutput(id) {
      var moduleName = this.getModuleFromNodeId(id);
      var infoNode = this.getNodeFromId(id);
      var numOutputs = Object.keys(infoNode.outputs).length;

      if (this.module === moduleName) {
        //Draw output
        var output = document.createElement('div');
        output.classList.add("output");
        output.classList.add("output_" + (numOutputs + 1));
        var parent = document.querySelector('#node-' + id + ' .outputs');
        parent.appendChild(output);
        this.updateConnectionNodes('node-' + id);
      }

      this.drawflow.drawflow[moduleName].data[id].outputs["output_" + (numOutputs + 1)] = {
        "connections": []
      };
      this.addHistory();
    }
  }, {
    key: "removeNodeInput",
    value: function removeNodeInput(id, input_class) {
      var _this3 = this;

      var moduleName = this.getModuleFromNodeId(id);
      var infoNode = this.getNodeFromId(id);

      if (this.module === moduleName) {
        document.querySelector('#node-' + id + ' .inputs .input.' + input_class).parentNode.removeChild(document.querySelector('#node-' + id + ' .inputs .input.' + input_class));
      }

      var removeInputs = [];
      Object.keys(infoNode.inputs[input_class].connections).map(function (key, index) {
        var id_output = infoNode.inputs[input_class].connections[index].node;
        var output_class = infoNode.inputs[input_class].connections[index].input;
        removeInputs.push({
          id_output: id_output,
          id: id,
          output_class: output_class,
          input_class: input_class
        });
      }); // Remove connections

      removeInputs.forEach(function (item, i) {
        _this3.removeSingleConnection(item.id_output, item.id, item.output_class, item.input_class);
      });
      delete this.drawflow.drawflow[moduleName].data[id].inputs[input_class]; // Update connection

      var connections = [];
      var connectionsInputs = this.drawflow.drawflow[moduleName].data[id].inputs;
      Object.keys(connectionsInputs).map(function (key, index) {
        connections.push(connectionsInputs[key]);
      });
      this.drawflow.drawflow[moduleName].data[id].inputs = {};
      var input_class_id = input_class.slice(6);
      var nodeUpdates = [];
      connections.forEach(function (item, i) {
        item.connections.forEach(function (itemx, f) {
          nodeUpdates.push(itemx);
        });
        _this3.drawflow.drawflow[moduleName].data[id].inputs['input_' + (i + 1)] = item;
      });
      nodeUpdates = new Set(nodeUpdates.map(function (e) {
        return JSON.stringify(e);
      }));
      nodeUpdates = Array.from(nodeUpdates).map(function (e) {
        return JSON.parse(e);
      });

      if (this.module === moduleName) {
        var eles = document.querySelectorAll("#node-" + id + " .inputs .input");
        eles.forEach(function (item, i) {
          var id_class = item.classList[1].slice(6);

          if (input_class_id < id_class) {
            item.classList.remove('input_' + id_class);
            item.classList.add('input_' + (id_class - 1));
          }
        });
      }

      nodeUpdates.forEach(function (itemx, i) {
        _this3.drawflow.drawflow[moduleName].data[itemx.node].outputs[itemx.input].connections.forEach(function (itemz, g) {
          if (itemz.node == id) {
            var output_id = itemz.output.slice(6);

            if (input_class_id < output_id) {
              if (_this3.module === moduleName) {
                var ele = document.querySelector(".connection.node_in_node-" + id + ".node_out_node-" + itemx.node + "." + itemx.input + ".input_" + output_id);
                ele.classList.remove('input_' + output_id);
                ele.classList.add('input_' + (output_id - 1));
              }

              if (itemz.points) {
                _this3.drawflow.drawflow[moduleName].data[itemx.node].outputs[itemx.input].connections[g] = {
                  node: itemz.node,
                  output: 'input_' + (output_id - 1),
                  points: itemz.points
                };
              } else {
                _this3.drawflow.drawflow[moduleName].data[itemx.node].outputs[itemx.input].connections[g] = {
                  node: itemz.node,
                  output: 'input_' + (output_id - 1)
                };
              }
            }
          }
        });
      });
      this.updateConnectionNodes('node-' + id);
      this.addHistory();
    }
  }, {
    key: "removeNodeOutput",
    value: function removeNodeOutput(id, output_class) {
      var _this4 = this;

      var moduleName = this.getModuleFromNodeId(id);
      var infoNode = this.getNodeFromId(id);

      if (this.module === moduleName) {
        //document.querySelector('#node-' + id + ' .outputs .output.' + output_class).remove();
        document.querySelector('#node-' + id + ' .outputs .output.' + output_class).parentNode.removeChild(document.querySelector('#node-' + id + ' .outputs .output.' + output_class));
      }

      var removeOutputs = [];
      Object.keys(infoNode.outputs[output_class].connections).map(function (key, index) {
        var id_input = infoNode.outputs[output_class].connections[index].node;
        var input_class = infoNode.outputs[output_class].connections[index].output;
        removeOutputs.push({
          id: id,
          id_input: id_input,
          output_class: output_class,
          input_class: input_class
        });
      }); // Remove connections

      removeOutputs.forEach(function (item, i) {
        _this4.removeSingleConnection(item.id, item.id_input, item.output_class, item.input_class);
      });
      delete this.drawflow.drawflow[moduleName].data[id].outputs[output_class]; // Update connection

      var connections = [];
      var connectionsOuputs = this.drawflow.drawflow[moduleName].data[id].outputs;
      Object.keys(connectionsOuputs).map(function (key, index) {
        connections.push(connectionsOuputs[key]);
      });
      this.drawflow.drawflow[moduleName].data[id].outputs = {};
      var output_class_id = output_class.slice(7);
      var nodeUpdates = [];
      connections.forEach(function (item, i) {
        item.connections.forEach(function (itemx, f) {
          nodeUpdates.push({
            node: itemx.node,
            output: itemx.output
          });
        });
        _this4.drawflow.drawflow[moduleName].data[id].outputs['output_' + (i + 1)] = item;
      });
      nodeUpdates = new Set(nodeUpdates.map(function (e) {
        return JSON.stringify(e);
      }));
      nodeUpdates = Array.from(nodeUpdates).map(function (e) {
        return JSON.parse(e);
      });

      if (this.module === moduleName) {
        var eles = document.querySelectorAll("#node-" + id + " .outputs .output");
        eles.forEach(function (item, i) {
          var id_class = item.classList[1].slice(7);

          if (output_class_id < id_class) {
            item.classList.remove('output_' + id_class);
            item.classList.add('output_' + (id_class - 1));
          }
        });
      }

      nodeUpdates.forEach(function (itemx, i) {
        _this4.drawflow.drawflow[moduleName].data[itemx.node].inputs[itemx.output].connections.forEach(function (itemz, g) {
          if (itemz.node == id) {
            var input_id = itemz.input.slice(7);

            if (output_class_id < input_id) {
              if (_this4.module === moduleName) {
                var ele = document.querySelector(".connection.node_in_node-" + itemx.node + ".node_out_node-" + id + ".output_" + input_id + "." + itemx.output);
                ele.classList.remove('output_' + input_id);
                ele.classList.remove(itemx.output);
                ele.classList.add('output_' + (input_id - 1));
                ele.classList.add(itemx.output);
              }

              if (itemz.points) {
                _this4.drawflow.drawflow[moduleName].data[itemx.node].inputs[itemx.output].connections[g] = {
                  node: itemz.node,
                  input: 'output_' + (input_id - 1),
                  points: itemz.points
                };
              } else {
                _this4.drawflow.drawflow[moduleName].data[itemx.node].inputs[itemx.output].connections[g] = {
                  node: itemz.node,
                  input: 'output_' + (input_id - 1)
                };
              }
            }
          }
        });
      });
      this.updateConnectionNodes('node-' + id);
      this.addHistory();
    }
  }, {
    key: "removeNodeId",
    value: function removeNodeId(id) {
      this.removeConnectionNodeId(id, false);
      var moduleName = this.getModuleFromNodeId(id.slice(5));
      var classSelect = id.replace("node-", "");

      if (this.module === moduleName) {
        //document.getElementById(id).remove();
        document.getElementById(id).parentNode.removeChild(document.getElementById(id));
      }

      delete this.drawflow.drawflow[moduleName].data[id.slice(5)];
      this.showDataToLeft();
      this.dispatch('nodeRemoved', id.slice(5));
      this.addHistory();
    }
  }, {
    key: "removeConnection",
    value: function removeConnection() {
      if (this.connection_selected != null) {
        var listclass = this.connection_selected.parentNode.getAttribute('class').split(" ");
        arr_delete_connection.push(listclass[1] + ' ' + listclass[2]);
        //this.connection_selected.parentElement.remove();
        this.connection_selected.parentNode.parentNode.removeChild(this.connection_selected.parentNode);
        //console.log(listclass);
        var index_out = this.drawflow.drawflow[this.module].data[listclass[2].slice(14)].outputs[listclass[3]].connections.findIndex(function (item, i) {
          return item.node === listclass[1].slice(13) && item.output === listclass[4];
        });
        this.drawflow.drawflow[this.module].data[listclass[2].slice(14)].outputs[listclass[3]].connections.splice(index_out, 1);
        var index_in = this.drawflow.drawflow[this.module].data[listclass[1].slice(13)].inputs[listclass[4]].connections.findIndex(function (item, i) {
          return item.node === listclass[2].slice(14) && item.input === listclass[3];
        });
        this.drawflow.drawflow[this.module].data[listclass[1].slice(13)].inputs[listclass[4]].connections.splice(index_in, 1);
        this.dispatch('connectionRemoved', {
          output_id: listclass[2].slice(14),
          input_id: listclass[1].slice(13),
          output_class: listclass[3],
          input_class: listclass[4]
        });
        this.connection_selected = null;
        this.addHistory();
      }
    }
  }, {
    key: "removeSingleConnection",
    value: function removeSingleConnection(id_output, id_input, output_class, input_class) {
      var nodeOneModule = this.getModuleFromNodeId(id_output);
      var nodeTwoModule = this.getModuleFromNodeId(id_input);

      if (nodeOneModule === nodeTwoModule) {
        // Check nodes in same module.
        // Check connection exist
        var exists = this.drawflow.drawflow[nodeOneModule].data[id_output].outputs[output_class].connections.findIndex(function (item, i) {
          return item.node == id_input && item.output === input_class;
        });

        if (exists > -1) {
          if (this.module === nodeOneModule) {
            // In same module with view.
            //document.querySelector('.connection.node_in_node-' + id_input + '.node_out_node-' + id_output + '.' + output_class + '.' + input_class).remove();
            document.querySelector('.connection.node_in_node-' + id_input + '.node_out_node-' + id_output + '.' + output_class + '.' + input_class).parentNode.removeChild(document.querySelector('.connection.node_in_node-' + id_input + '.node_out_node-' + id_output + '.' + output_class + '.' + input_class));
          }

          var index_out = this.drawflow.drawflow[nodeOneModule].data[id_output].outputs[output_class].connections.findIndex(function (item, i) {
            return item.node == id_input && item.output === input_class;
          });
          this.drawflow.drawflow[nodeOneModule].data[id_output].outputs[output_class].connections.splice(index_out, 1);
          var index_in = this.drawflow.drawflow[nodeOneModule].data[id_input].inputs[input_class].connections.findIndex(function (item, i) {
            return item.node == id_output && item.input === output_class;
          });
          this.drawflow.drawflow[nodeOneModule].data[id_input].inputs[input_class].connections.splice(index_in, 1);
          this.dispatch('connectionRemoved', {
            output_id: id_output,
            input_id: id_input,
            output_class: output_class,
            input_class: input_class
          });
          this.addHistory();
          return true;
        } else {
          return false;
        }
      } else {
        return false;
      }
    }
  }, {
    key: "removeConnectionNodeId",
    value: function removeConnectionNodeId(id, saveHistory) {
      var idSearchIn = 'node_in_' + id;
      var idSearchOut = 'node_out_' + id;
      var elemsOut = document.getElementsByClassName(idSearchOut);

      for (var i = elemsOut.length - 1; i >= 0; i--) {
        var listclass = elemsOut[i].getAttribute('class').split(' ');
        var index_in = this.drawflow.drawflow[this.module].data[listclass[1].slice(13)].inputs[listclass[4]].connections.filter(function (item, i) {
          return item.node != listclass[2].slice(14) && item.input === listclass[3];
        });
        this.drawflow.drawflow[this.module].data[listclass[1].slice(13)].inputs[listclass[4]].connections = index_in;
        // this.drawflow.drawflow[this.module].data[listclass[1].slice(13)].inputs[listclass[4]].connections.splice(index_in, 1);
        var index_out = this.drawflow.drawflow[this.module].data[listclass[2].slice(14)].outputs[listclass[3]].connections.filter(function (item, i) {
          return item.node != listclass[1].slice(13) && item.output === listclass[4];
        });
        elemsOut[i].parentNode.removeChild(elemsOut[i]);
        // this.drawflow.drawflow[this.module].data[listclass[2].slice(14)].outputs[listclass[3]].connections.splice(index_out, 1);
        this.drawflow.drawflow[this.module].data[listclass[2].slice(14)].outputs[listclass[3]].connections = index_out;
        // elemsOut[i].remove();
        this.dispatch('connectionRemoved', {
          output_id: listclass[2].slice(14),
          input_id: listclass[1].slice(13),
          output_class: listclass[3],
          input_class: listclass[4]
        });
      }

      var elemsIn = document.getElementsByClassName(idSearchIn);

      for (var i = elemsIn.length - 1; i >= 0; i--) {
        var listclass = elemsIn[i].getAttribute('class').split(' ');
        var index_out = this.drawflow.drawflow[this.module].data[listclass[2].slice(14)].outputs[listclass[3]].connections.filter(function (item, i) {
          return item.node === listclass[1].slice(13) && item.output === listclass[4];
        });
        this.drawflow.drawflow[this.module].data[listclass[2].slice(14)].outputs[listclass[3]].connections.splice(index_out, 1);
        var index_in = this.drawflow.drawflow[this.module].data[listclass[1].slice(13)].inputs[listclass[4]].connections.filter(function (item, i) {
          return item.node === listclass[2].slice(14) && item.input === listclass[3];
        });
        this.drawflow.drawflow[this.module].data[listclass[1].slice(13)].inputs[listclass[4]].connections.splice(index_in, 1);
        //elemsIn[i].remove();
        elemsIn[i].parentNode.removeChild(elemsIn[i]);
        this.dispatch('connectionRemoved', {
          output_id: listclass[2].slice(14),
          input_id: listclass[1].slice(13),
          output_class: listclass[3],
          input_class: listclass[4]
        });
      }
      if (saveHistory) {
        this.addHistory();
      }
    }
  }, {
    key: "getModuleFromNodeId",
    value: function getModuleFromNodeId(id) {
      var nameModule;
      var editor = this.drawflow.drawflow;
      Object.keys(editor).map(function (moduleName, index) {
        Object.keys(editor[moduleName].data).map(function (node, index2) {
          if (node == id) {
            nameModule = moduleName;
          }
        });
      });
      return nameModule;
    }
  }, {
    key: "addModule",
    value: function addModule(name) {
      this.drawflow.drawflow[name] = {
        "data": {}
      };
      this.dispatch('moduleCreated', name);
    }
  }, {
    key: "changeModule",
    value: function changeModule(name) {
      this.dispatch('moduleChanged', name);
      this.module = name;
      this.precanvas.innerHTML = "";
      this.canvas_x = 0;
      this.canvas_y = 0;
      this.pos_x = 0;
      this.pos_y = 0;
      this.mouse_x = 0;
      this.mouse_y = 0;
      this.zoom = 1;
      this.precanvas.style.transform = '';
      this.import(this.drawflow);
    }
  }, {
    key: "removeModule",
    value: function removeModule(name) {
      if (this.module === name) {
        this.changeModule('Home');
      }

      delete this.drawflow.drawflow[name];
      this.dispatch('moduleRemoved', name);
    }
  }, {
    key: "clearModuleSelected",
    value: function clearModuleSelected() {
      this.precanvas.innerHTML = "";
      this.drawflow.drawflow[this.module] = {
        "data": {}
      };
    }
  }, {
    key: "clear",
    value: function clear() {
      this.precanvas.innerHTML = "";
      this.drawflow = {
        "drawflow": {
          "Home": {
            "data": {}
          }
        }
      };
      // this.addHistory();
    }
    // }, {
    //   key: "export",
    //   value: function _export() {
    //     return JSON.parse(JSON.stringify(this.drawflow));
    //   }
    // }, {
    //   key: "import",
    //   value: function _import(data) {
    //     this.clear();
    //     this.drawflow = JSON.parse(JSON.stringify(data));
    //     this.load();
    //     this.dispatch('import', 'import');
    //   }
    /* Events */

  }, {
    key: "on",
    value: function on(event, callback) {
      // Check if the callback is not a function
      if (typeof callback !== 'function') {
        console.error("The listener callback must be a function, the given type is ".concat(_typeof(callback)));
        return false;
      } // Check if the event is not a string


      if (typeof event !== 'string') {
        console.error("The event name must be a string, the given type is ".concat(_typeof(event)));
        return false;
      } // Check if this event not exists


      if (this.events[event] === undefined) {
        this.events[event] = {
          listeners: []
        };
      }

      this.events[event].listeners.push(callback);
    }
  }, {
    key: "removeListener",
    value: function removeListener(event, callback) {
      // Check if this event not exists
      if (this.events[event] === undefined) {
        //console.error(`This event: ${event} does not exist`);
        return false;
      }

      this.events[event].listeners = this.events[event].listeners.filter(function (listener) {
        return listener.toString() !== callback.toString();
      });
    }
  }, {
    key: "dispatch",
    value: function dispatch(event, details) {
      // Check if this event not exists
      if (this.events[event] === undefined) {
        // console.error(`This event: ${event} does not exist`);
        return false;
      }

      this.events[event].listeners.forEach(function (listener) {
        listener(details);
      });
    }
  }, {
    key: "addHistory",
    value: function addHistory(){
      if (this.preventHistoryEvent) return;
      if (!this.isChanged()) return;
      if (this.currentHitoryIndex >= this.history.length - 1){
        this.history.push($.extend(true,{},this.drawflow.drawflow));
        if (this.history.length > this.maximumHistories) this.history.shift();
        this.currentHitoryIndex = this.history.length - 1;
      }else{
        this.history.length = this.currentHitoryIndex + 1;
        this.addHistory();
      }
    }
  }, {
    key: "clearHistory",
    value: function clearHistory(){
      if (this.preventHistoryEvent) return;
      this.history.length = 0;
    }
  }, {
    key: "isChanged",
    value: function isChanged(){
      return JSON.stringify(this.drawflow.drawflow) != JSON.stringify(this.history[this.currentHitoryIndex])
    }
  }, {
    key: "undo",
    value: function undo(){
      if (this.preventHistoryEvent) return;
      if (this.history.length){
        if (this.isChanged()){
          this.addHistory();
          this.undo();
        }else if (this.currentHitoryIndex){
          this.currentHitoryIndex--;
          this.preventHistoryEvent = true;
          this.clear();
          this.drawflow.drawflow = $.extend(true,{},this.history[this.currentHitoryIndex]);
          this.load();
          this.preventHistoryEvent = false;
        }
      }
    }
  }, {
    key: "redo",
    value: function redo(){
      if (this.preventHistoryEvent) return;
      if (this.history.length && this.currentHitoryIndex < this.history.length - 1){
        this.currentHitoryIndex++;
        this.preventHistoryEvent = true;
        this.clear();
        this.drawflow.drawflow = $.extend(true,{},this.history[this.currentHitoryIndex]);
        this.load();
        this.preventHistoryEvent = false;
      }
    }
  }, {
    key: "focusData",
    value: function focusData(e, drawclick, id) {
        $('.drawflow').children().map(function(i, val) {
            if (val.childNodes && val.childNodes.length > 0 && val.childNodes[0].tagName == 'DIV') {
                val.childNodes[0].setAttribute("class", val.childNodes[0].getAttribute("class").replace(" selected", ""))
            }
        });
        //remove css svg and css target
        $('.connection').map(function(i, val) {
          if (val && val.childNodes.length > 0 && val.childNodes[0].tagName == 'path') {
              val.childNodes[0].setAttribute("class", val.childNodes[0].getAttribute("class").replace(" target-trigger", ""))
              $(val).css('z-index', '0');
          }
        });
        $('.content-scenario').children().map(function(i, item) {
            if (item.tagName == 'P') {
                item.setAttribute("class", item.getAttribute("class").replace(" focus-data", ""))
            }
        });
        $('.content-qa').children().map(function(i, item) {
            if (item.tagName == 'P') {
                item.setAttribute("class", item.getAttribute("class").replace(" focus-data", ""))
            }
        });
        // $('.edit-btn-scenario').val('')
        //click node focus
        if (drawclick) {
            let selector = e.getAttribute("id").replace('node-', '');
            var elem = $('#node-' + selector);
            elem.addClass("selected");
            var className = e.getAttribute("id").replace("node-", "")
            $('.' + className).addClass("focus-data");
            var addIdEdit = e.getAttribute("id").replace("node-", "");
            $('.connection').map(function(i, val) {
              if (val.childNodes && val.childNodes.length > 0 && val.childNodes[0].tagName == 'path') {
                var classList = val.getAttribute('class').split(' ');
                if (classList[1] == 'node_in_node-'+selector || classList[2] == 'node_out_node-'+selector) {
                  $(val.childNodes[0]).addClass('target-trigger')
                  $(val).css('z-index', '1');
                }
              }
            });
        }
        //add node focus
        if (id) {
            var elem = $('#node-' + id);
            elem.addClass("selected");
            // $('#drawflow').animate({
            //     scrollTop: elem.position().top - (($('#drawflow').height() - elem.height()) / 2),
            //     scrollLeft: elem.position().left - (($('#drawflow').width() - elem.width()) / 2)
            // }, {
            //     duration: 300,
            // });
            $('.' + id).addClass("focus-data");
            var addIdEdit = id;
            $('.connection').map(function(i, val) {
              if (val.childNodes && val.childNodes.length > 0 && val.childNodes[0].tagName == 'path') {
                var classList = val.getAttribute('class').split(' ');
                if (classList[1] == 'node_in_node-'+ addIdEdit || classList[2] == 'node_out_node-' + addIdEdit) {
                  $(val.childNodes[0]).addClass('target-trigger')
                  $(val).css('z-index', '1');
                }
              }
            });
        }
        //content click focus
        if (!drawclick && !id) {
            var elem = $('#node-' + e.getAttribute("class"))
            elem.addClass("selected");
            $('#drawflow').animate({
                scrollTop: elem.position().top - (($('#drawflow').height() - elem.height()) / 2),
                scrollLeft: elem.position().left - (($('#drawflow').width() - elem.width()) / 2)
            }, {
                duration: 300,
            });
            $('.' + e.getAttribute("class")).addClass("focus-data");
            var addIdEdit = e.getAttribute("class").replace(" focus-data", "");
            $('.connection').map(function(i, val) {
              if (val.childNodes && val.childNodes.length > 0 && val.childNodes[0].tagName == 'path') {
                var classList = val.getAttribute('class').split(' ');
                if (classList[1] == 'node_in_node-'+ addIdEdit || classList[2] == 'node_out_node-' + addIdEdit) {
                  $(val.childNodes[0]).addClass('target-trigger')
                  $(val).css('z-index', '1');
                }
              }
            });
        }
        $('#qaModal').modal('hide');
    }
  },{
    key: "showDataToLeft",
    value: function showDataToLeft() {
      var data = this.drawflow.drawflow[this.module].data;
      $('.content-qa').innerHTML = '';
      $('.content-scenario').innerHTML = '';
      let html_dataQa = new Array();
      let html_dataScenario = new Array();
      if (data) {
        $.each(data, function(index, value) {
          let html = '<p class="' + index + '" onclick="editor.focusData(this)">' + value.name + '</p>';
          if (index.substr(0, 1) == 'q') {
            html_dataQa.push(html);
          }
          if (index.substr(0, 1) == 's') {
            html_dataScenario.push(html);
          }
        });
      }
      $('.content-qa').html(html_dataQa);
      $('.content-scenario').html(html_dataScenario);
    }
  }]);

  return Drawflow;
}();