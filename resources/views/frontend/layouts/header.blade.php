<!DOCTYPE html>
<html lang="en-US" class="no-js">
  <head>
    <meta charset="UTF-8" />

    <script>
      class RocketLazyLoadScripts {
        constructor() {
          (this.v = "1.2.3"),
            (this.triggerEvents = [
              "keydown",
              "mousedown",
              "mousemove",
              "touchmove",
              "touchstart",
              "touchend",
              "wheel",
            ]),
            (this.userEventHandler = this._triggerListener.bind(this)),
            (this.touchStartHandler = this._onTouchStart.bind(this)),
            (this.touchMoveHandler = this._onTouchMove.bind(this)),
            (this.touchEndHandler = this._onTouchEnd.bind(this)),
            (this.clickHandler = this._onClick.bind(this)),
            (this.interceptedClicks = []),
            window.addEventListener("pageshow", (t) => {
              this.persisted = t.persisted;
            }),
            window.addEventListener("DOMContentLoaded", () => {
              this._preconnect3rdParties();
            }),
            (this.delayedScripts = { normal: [], async: [], defer: [] }),
            (this.trash = []),
            (this.allJQueries = []);
        }
        _addUserInteractionListener(t) {
          if (document.hidden) {
            t._triggerListener();
            return;
          }
          this.triggerEvents.forEach((e) =>
            window.addEventListener(e, t.userEventHandler, { passive: !0 })
          ),
            window.addEventListener("touchstart", t.touchStartHandler, {
              passive: !0,
            }),
            window.addEventListener("mousedown", t.touchStartHandler),
            document.addEventListener("visibilitychange", t.userEventHandler);
        }
        _removeUserInteractionListener() {
          this.triggerEvents.forEach((t) =>
            window.removeEventListener(t, this.userEventHandler, {
              passive: !0,
            })
          ),
            document.removeEventListener(
              "visibilitychange",
              this.userEventHandler
            );
        }
        _onTouchStart(t) {
          "HTML" !== t.target.tagName &&
            (window.addEventListener("touchend", this.touchEndHandler),
            window.addEventListener("mouseup", this.touchEndHandler),
            window.addEventListener("touchmove", this.touchMoveHandler, {
              passive: !0,
            }),
            window.addEventListener("mousemove", this.touchMoveHandler),
            t.target.addEventListener("click", this.clickHandler),
            this._renameDOMAttribute(t.target, "onclick", "rocket-onclick"),
            this._pendingClickStarted());
        }
        _onTouchMove(t) {
          window.removeEventListener("touchend", this.touchEndHandler),
            window.removeEventListener("mouseup", this.touchEndHandler),
            window.removeEventListener("touchmove", this.touchMoveHandler, {
              passive: !0,
            }),
            window.removeEventListener("mousemove", this.touchMoveHandler),
            t.target.removeEventListener("click", this.clickHandler),
            this._renameDOMAttribute(t.target, "rocket-onclick", "onclick"),
            this._pendingClickFinished();
        }
        _onTouchEnd(t) {
          window.removeEventListener("touchend", this.touchEndHandler),
            window.removeEventListener("mouseup", this.touchEndHandler),
            window.removeEventListener("touchmove", this.touchMoveHandler, {
              passive: !0,
            }),
            window.removeEventListener("mousemove", this.touchMoveHandler);
        }
        _onClick(t) {
          t.target.removeEventListener("click", this.clickHandler),
            this._renameDOMAttribute(t.target, "rocket-onclick", "onclick"),
            this.interceptedClicks.push(t),
            t.preventDefault(),
            t.stopPropagation(),
            t.stopImmediatePropagation(),
            this._pendingClickFinished();
        }
        _replayClicks() {
          window.removeEventListener("touchstart", this.touchStartHandler, {
            passive: !0,
          }),
            window.removeEventListener("mousedown", this.touchStartHandler),
            this.interceptedClicks.forEach((t) => {
              t.target.dispatchEvent(
                new MouseEvent("click", {
                  view: t.view,
                  bubbles: !0,
                  cancelable: !0,
                })
              );
            });
        }
        _waitForPendingClicks() {
          return new Promise((t) => {
            this._isClickPending ? (this._pendingClickFinished = t) : t();
          });
        }
        _pendingClickStarted() {
          this._isClickPending = !0;
        }
        _pendingClickFinished() {
          this._isClickPending = !1;
        }
        _renameDOMAttribute(t, e, r) {
          t.hasAttribute &&
            t.hasAttribute(e) &&
            (event.target.setAttribute(r, event.target.getAttribute(e)),
            event.target.removeAttribute(e));
        }
        _triggerListener() {
          this._removeUserInteractionListener(this),
            "loading" === document.readyState
              ? document.addEventListener(
                  "DOMContentLoaded",
                  this._loadEverythingNow.bind(this)
                )
              : this._loadEverythingNow();
        }
        _preconnect3rdParties() {
          let t = [];
          document
            .querySelectorAll("script[type=rocketlazyloadscript]")
            .forEach((e) => {
              if (e.hasAttribute("src")) {
                let r = new URL(e.src).origin;
                r !== location.origin &&
                  t.push({
                    src: r,
                    crossOrigin:
                      e.crossOrigin ||
                      "module" === e.getAttribute("data-rocket-type"),
                  });
              }
            }),
            (t = [...new Map(t.map((t) => [JSON.stringify(t), t])).values()]),
            this._batchInjectResourceHints(t, "preconnect");
        }
        async _loadEverythingNow() {
          (this.lastBreath = Date.now()),
            this._delayEventListeners(this),
            this._delayJQueryReady(this),
            this._handleDocumentWrite(),
            this._registerAllDelayedScripts(),
            this._preloadAllScripts(),
            await this._loadScriptsFromList(this.delayedScripts.normal),
            await this._loadScriptsFromList(this.delayedScripts.defer),
            await this._loadScriptsFromList(this.delayedScripts.async);
          try {
            await this._triggerDOMContentLoaded(),
              await this._triggerWindowLoad();
          } catch (t) {
            console.error(t);
          }
          window.dispatchEvent(new Event("rocket-allScriptsLoaded")),
            this._waitForPendingClicks().then(() => {
              this._replayClicks();
            }),
            this._emptyTrash();
        }
        _registerAllDelayedScripts() {
          document
            .querySelectorAll("script[type=rocketlazyloadscript]")
            .forEach((t) => {
              t.hasAttribute("data-rocket-src")
                ? t.hasAttribute("async") && !1 !== t.async
                  ? this.delayedScripts.async.push(t)
                  : (t.hasAttribute("defer") && !1 !== t.defer) ||
                    "module" === t.getAttribute("data-rocket-type")
                  ? this.delayedScripts.defer.push(t)
                  : this.delayedScripts.normal.push(t)
                : this.delayedScripts.normal.push(t);
            });
        }
        async _transformScript(t) {
          return new Promise(
            (await this._littleBreath(),
            navigator.userAgent.indexOf("Firefox/") > 0 ||
              "" === navigator.vendor)
              ? (e) => {
                  let r = document.createElement("script");
                  [...t.attributes].forEach((t) => {
                    let e = t.nodeName;
                    "type" !== e &&
                      ("data-rocket-type" === e && (e = "type"),
                      "data-rocket-src" === e && (e = "src"),
                      r.setAttribute(e, t.nodeValue));
                  }),
                    t.text && (r.text = t.text),
                    r.hasAttribute("src")
                      ? (r.addEventListener("load", e),
                        r.addEventListener("error", e))
                      : ((r.text = t.text), e());
                  try {
                    t.parentNode.replaceChild(r, t);
                  } catch (i) {
                    e();
                  }
                }
              : async (e) => {
                  function r() {
                    t.setAttribute("data-rocket-status", "failed"), e();
                  }
                  try {
                    let i = t.getAttribute("data-rocket-type"),
                      n = t.getAttribute("data-rocket-src");
                    t.text,
                      i
                        ? ((t.type = i), t.removeAttribute("data-rocket-type"))
                        : t.removeAttribute("type"),
                      t.addEventListener("load", function r() {
                        t.setAttribute("data-rocket-status", "executed"), e();
                      }),
                      t.addEventListener("error", r),
                      n
                        ? (t.removeAttribute("data-rocket-src"), (t.src = n))
                        : (t.src =
                            "data:text/javascript;base64," +
                            window.btoa(unescape(encodeURIComponent(t.text))));
                  } catch (s) {
                    r();
                  }
                }
          );
        }
        async _loadScriptsFromList(t) {
          let e = t.shift();
          return e && e.isConnected
            ? (await this._transformScript(e), this._loadScriptsFromList(t))
            : Promise.resolve();
        }
        _preloadAllScripts() {
          this._batchInjectResourceHints(
            [
              ...this.delayedScripts.normal,
              ...this.delayedScripts.defer,
              ...this.delayedScripts.async,
            ],
            "preload"
          );
        }
        _batchInjectResourceHints(t, e) {
          var r = document.createDocumentFragment();
          t.forEach((t) => {
            let i =
              (t.getAttribute && t.getAttribute("data-rocket-src")) || t.src;
            if (i) {
              let n = document.createElement("link");
              (n.href = i),
                (n.rel = e),
                "preconnect" !== e && (n.as = "script"),
                t.getAttribute &&
                  "module" === t.getAttribute("data-rocket-type") &&
                  (n.crossOrigin = !0),
                t.crossOrigin && (n.crossOrigin = t.crossOrigin),
                t.integrity && (n.integrity = t.integrity),
                r.appendChild(n),
                this.trash.push(n);
            }
          }),
            document.head.appendChild(r);
        }
        _delayEventListeners(t) {
          let e = {};
          function r(t, r) {
            !(function t(r) {
              !e[r] &&
                ((e[r] = {
                  originalFunctions: {
                    add: r.addEventListener,
                    remove: r.removeEventListener,
                  },
                  eventsToRewrite: [],
                }),
                (r.addEventListener = function () {
                  (arguments[0] = i(arguments[0])),
                    e[r].originalFunctions.add.apply(r, arguments);
                }),
                (r.removeEventListener = function () {
                  (arguments[0] = i(arguments[0])),
                    e[r].originalFunctions.remove.apply(r, arguments);
                }));
              function i(t) {
                return e[r].eventsToRewrite.indexOf(t) >= 0 ? "rocket-" + t : t;
              }
            })(t),
              e[t].eventsToRewrite.push(r);
          }
          function i(t, e) {
            let r = t[e];
            Object.defineProperty(t, e, {
              get: () => r || function () {},
              set(i) {
                t["rocket" + e] = r = i;
              },
            });
          }
          r(document, "DOMContentLoaded"),
            r(window, "DOMContentLoaded"),
            r(window, "load"),
            r(window, "pageshow"),
            r(document, "readystatechange"),
            i(document, "onreadystatechange"),
            i(window, "onload"),
            i(window, "onpageshow");
        }
        _delayJQueryReady(t) {
          let e;
          function r(r) {
            if (r && r.fn && !t.allJQueries.includes(r)) {
              r.fn.ready = r.fn.init.prototype.ready = function (e) {
                return (
                  t.domReadyFired
                    ? e.bind(document)(r)
                    : document.addEventListener("rocket-DOMContentLoaded", () =>
                        e.bind(document)(r)
                      ),
                  r([])
                );
              };
              let i = r.fn.on;
              (r.fn.on = r.fn.init.prototype.on =
                function () {
                  if (this[0] === window) {
                    function t(t) {
                      return t
                        .split(" ")
                        .map((t) =>
                          "load" === t || 0 === t.indexOf("load.")
                            ? "rocket-jquery-load"
                            : t
                        )
                        .join(" ");
                    }
                    "string" == typeof arguments[0] ||
                    arguments[0] instanceof String
                      ? (arguments[0] = t(arguments[0]))
                      : "object" == typeof arguments[0] &&
                        Object.keys(arguments[0]).forEach((e) => {
                          let r = arguments[0][e];
                          delete arguments[0][e], (arguments[0][t(e)] = r);
                        });
                  }
                  return i.apply(this, arguments), this;
                }),
                t.allJQueries.push(r);
            }
            e = r;
          }
          r(window.jQuery),
            Object.defineProperty(window, "jQuery", {
              get: () => e,
              set(t) {
                r(t);
              },
            });
        }
        async _triggerDOMContentLoaded() {
          (this.domReadyFired = !0),
            await this._littleBreath(),
            document.dispatchEvent(new Event("rocket-DOMContentLoaded")),
            await this._littleBreath(),
            window.dispatchEvent(new Event("rocket-DOMContentLoaded")),
            await this._littleBreath(),
            document.dispatchEvent(new Event("rocket-readystatechange")),
            await this._littleBreath(),
            document.rocketonreadystatechange &&
              document.rocketonreadystatechange();
        }
        async _triggerWindowLoad() {
          await this._littleBreath(),
            window.dispatchEvent(new Event("rocket-load")),
            await this._littleBreath(),
            window.rocketonload && window.rocketonload(),
            await this._littleBreath(),
            this.allJQueries.forEach((t) =>
              t(window).trigger("rocket-jquery-load")
            ),
            await this._littleBreath();
          let t = new Event("rocket-pageshow");
          (t.persisted = this.persisted),
            window.dispatchEvent(t),
            await this._littleBreath(),
            window.rocketonpageshow &&
              window.rocketonpageshow({ persisted: this.persisted });
        }
        _handleDocumentWrite() {
          let t = new Map();
          document.write = document.writeln = function (e) {
            let r = document.currentScript;
            r || console.error("WPRocket unable to document.write this: " + e);
            let i = document.createRange(),
              n = r.parentElement,
              s = t.get(r);
            void 0 === s && ((s = r.nextSibling), t.set(r, s));
            let a = document.createDocumentFragment();
            i.setStart(a, 0),
              a.appendChild(i.createContextualFragment(e)),
              n.insertBefore(a, s);
          };
        }
        async _littleBreath() {
          Date.now() - this.lastBreath > 45 &&
            (await this._requestAnimFrame(), (this.lastBreath = Date.now()));
        }
        async _requestAnimFrame() {
          return document.hidden
            ? new Promise((t) => setTimeout(t))
            : new Promise((t) => requestAnimationFrame(t));
        }
        _emptyTrash() {
          this.trash.forEach((t) => t.remove());
        }
        static run() {
          let t = new RocketLazyLoadScripts();
          t._addUserInteractionListener(t);
        }
      }
      RocketLazyLoadScripts.run();
    </script>

    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="profile" href="//gmpg.org/xfn/11" />
    <title>Ziba &#8211; &#8211; Get Your Glam</title>
    <meta name="robots" content="max-image-preview:large" />
    <link rel="dns-prefetch" href="//fonts.googleapis.com" />
    <!-- Font Awesome v5 -->
   <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
   {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/> --}}


       <link
      data-minify="1"
      rel="stylesheet"
      id="redux-extendify-styles-css"
      href="https://zibabeauty.in/wp-content/cache/min/1/wp-content/plugins/redux-framework/redux-core/assets/css/extendify-utilities.css?ver=1717589898"
      type="text/css"
      media="all"
    />
    <link
      data-minify="1"
      rel="stylesheet"
      id="woof-css"
      href="https://zibabeauty.in/wp-content/cache/min/1/wp-content/plugins/woocommerce-products-filter/css/front.css?ver=1717589898"
      type="text/css"
      media="all"
    />
    <link
      rel="stylesheet"
      id="hara-theme-fonts-css"
      href="https://fonts.googleapis.com/css?family=Inter%3A400%2C500%2C600%2C700%7CCormorant%20Garamond%3A400%2C500%2C600%2C700&#038;subset=latin%2Clatin-ext&#038;display=swap"
      type="text/css"
      media="all"
    />
    <link
      rel="stylesheet"
      id="joinchat-css"
      href="https://zibabeauty.in/wp-content/plugins/creame-whatsapp-me/public/css/joinchat-btn.min.css?ver=4.5.20"
      type="text/css"
      media="all"
    />
    <link
      data-minify="1"
      rel="stylesheet"
      id="elementor-icons-css"
      href="https://zibabeauty.in/wp-content/cache/min/1/wp-content/plugins/elementor/assets/lib/eicons/css/elementor-icons.min.css?ver=1717589898"
      type="text/css"
      media="all"
    />
    <link
      rel="stylesheet"
      id="elementor-frontend-css"
      href="https://zibabeauty.in/wp-content/plugins/elementor/assets/css/frontend-lite.min.css?ver=3.14.1"
      type="text/css"
      media="all"
    />
    <style id="elementor-frontend-inline-css" type="text/css">
      .elementor-kit-6 {
        --e-global-color-primary: #6ec1e4;
        --e-global-color-secondary: #54595f;
        --e-global-color-text: #7a7a7a;
        --e-global-color-accent: #61ce70;
        --e-global-typography-primary-font-family: "Roboto";
        --e-global-typography-primary-font-weight: 600;
        --e-global-typography-secondary-font-family: "Roboto Slab";
        --e-global-typography-secondary-font-weight: 400;
        --e-global-typography-text-font-family: "Roboto";
        --e-global-typography-text-font-weight: 400;
        --e-global-typography-accent-font-family: "Roboto";
        --e-global-typography-accent-font-weight: 500;
      }
      .elementor-section.elementor-section-boxed > .elementor-container {
        max-width: 1356px !important;
      }
      .elementor-widget:not(:last-child) {
        margin-bottom: 20px;
      }
      .elementor-element {
        --widgets-spacing: 20px;
      }
       {
      }
      h1.page-title {
        display: var(--page-title-display);
      }
      @media (max-width: 1024px) {
        .elementor-section.elementor-section-boxed > .elementor-container {
          max-width: 1024px !important;
        }
      }
      @media (max-width: 767px) {
        .elementor-section.elementor-section-boxed > .elementor-container {
          max-width: 767px !important;
        }
      }
      .elementor-23
        .elementor-element.elementor-element-8909615
        > .elementor-container
        > .elementor-column
        > .elementor-widget-wrap {
        align-content: center;
        align-items: center;
      }
      .elementor-23 .elementor-element.elementor-element-6acbc01c {
        --e-image-carousel-slides-to-show: 1;
      }
      .elementor-23 .elementor-element.elementor-element-3da5d0a3 {
        --e-image-carousel-slides-to-show: 1;
      }
      .elementor-23 .elementor-element.elementor-element-d4a51af {
        transition: background 0.3s, border 0.3s, border-radius 0.3s,
          box-shadow 0.3s;
      }
      .elementor-23
        .elementor-element.elementor-element-d4a51af
        > .elementor-background-overlay {
        transition: background 0.3s, border-radius 0.3s, opacity 0.3s;
      }
      .elementor-23
        .elementor-element.elementor-element-4f5f7f5b
        > .elementor-element-populated {
        margin: 30px 30px 30px 30px;
        --e-column-margin-right: 30px;
        --e-column-margin-left: 30px;
      }
      .elementor-23
        .elementor-element.elementor-element-a2d5ce9
        .elementor-heading-title {
        color: #f6e9e4;
        font-family: "Allura", Sans-serif;
        font-size: 175px;
        font-weight: 100;
      }
      .elementor-23
        .elementor-element.elementor-element-a2d5ce9
        > .elementor-widget-container {
        margin: 10px 10px 10px 10px;
      }
      .elementor-23 .elementor-element.elementor-element-159885d0 {
        text-align: center;
        top: 114px;
      }
      .elementor-23
        .elementor-element.elementor-element-159885d0
        .elementor-heading-title {
        color: #242424;
        font-family: "cocosharp", Sans-serif;
        font-size: 48px;
        font-weight: 100;
        letter-spacing: 1.5px;
        word-spacing: 11px;
      }
      .elementor-23
        .elementor-element.elementor-element-159885d0
        > .elementor-widget-container {
        margin: 20px 20px 20px 20px;
      }
      body:not(.rtl)
        .elementor-23
        .elementor-element.elementor-element-159885d0 {
        left: 22px;
      }
      body.rtl .elementor-23 .elementor-element.elementor-element-159885d0 {
        right: 22px;
      }
      .elementor-23 .elementor-element.elementor-element-d8c839 {
        --divider-border-style: solid;
        --divider-color: #000;
        --divider-border-width: 1px;
        top: 225px;
      }
      .elementor-23
        .elementor-element.elementor-element-d8c839
        .elementor-divider-separator {
        width: 10%;
        margin: 0 auto;
        margin-center: 0;
      }
      .elementor-23
        .elementor-element.elementor-element-d8c839
        .elementor-divider {
        text-align: center;
        padding-top: 20px;
        padding-bottom: 20px;
      }
      body:not(.rtl) .elementor-23 .elementor-element.elementor-element-d8c839 {
        left: 4px;
      }
      body.rtl .elementor-23 .elementor-element.elementor-element-d8c839 {
        right: 4px;
      }
      .elementor-23 .elementor-element.elementor-element-420c29f8 {
        --spacer-size: 50px;
      }
      .elementor-23 .elementor-element.elementor-element-720da442 {
        text-align: center;
        color: #000000;
      }
      .elementor-23
        .elementor-element.elementor-element-720da442
        > .elementor-widget-container {
        margin: 20px 20px 20px 20px;
      }
      .elementor-23 .elementor-element.elementor-element-625ecfb4 {
        --spacer-size: 50px;
      }
      .elementor-23
        .elementor-element.elementor-element-191772e3
        .elementor-button {
        background-color: #080707;
      }
      .elementor-23 .elementor-element.elementor-element-6dad0e2a {
        --spacer-size: 101px;
      }
      .elementor-bc-flex-widget
        .elementor-23
        .elementor-element.elementor-element-7cce41b1.elementor-column
        .elementor-widget-wrap {
        align-items: center;
      }
      .elementor-23
        .elementor-element.elementor-element-7cce41b1.elementor-column.elementor-element[data-element_type="column"]
        > .elementor-widget-wrap.elementor-element-populated {
        align-content: center;
        align-items: center;
      }
      .elementor-23
        .elementor-element.elementor-element-7cce41b1.elementor-column
        > .elementor-widget-wrap {
        justify-content: center;
      }
      .elementor-23
        .elementor-element.elementor-element-7cce41b1
        > .elementor-widget-wrap
        > .elementor-widget:not(.elementor-widget__width-auto):not(.elementor-widget__width-initial):not(:last-child):not(.elementor-absolute) {
        margin-bottom: 1px;
      }
      .elementor-23
        .elementor-element.elementor-element-7cce41b1:not(.elementor-motion-effects-element-type-background)
        > .elementor-widget-wrap,
      .elementor-23
        .elementor-element.elementor-element-7cce41b1
        > .elementor-widget-wrap
        > .elementor-motion-effects-container
        > .elementor-motion-effects-layer {
        background-image: url("https://zibabeauty.in/wp-content/uploads/2023/04/smiling-beautiful-woman-using-mascara-scaled-1-1.jpg");
        background-position: center center;
        background-repeat: no-repeat;
        background-size: cover;
      }
      .elementor-23
        .elementor-element.elementor-element-7cce41b1
        > .elementor-element-populated {
        transition: background 0.3s, border 0.3s, border-radius 0.3s,
          box-shadow 0.3s;
      }
      .elementor-23
        .elementor-element.elementor-element-7cce41b1
        > .elementor-element-populated
        > .elementor-background-overlay {
        transition: background 0.3s, border-radius 0.3s, opacity 0.3s;
      }
      .elementor-23 .elementor-element.elementor-element-731cba6a {
        --spacer-size: 151px;
      }
      .elementor-23
        .elementor-element.elementor-element-64c11ed3
        .elementor-button {
        fill: #02010100;
        color: #02010100;
        background-color: #02010100;
      }
      .elementor-23 .elementor-element.elementor-element-56b8a169 {
        text-align: center;
        bottom: -93px;
      }
      body:not(.rtl)
        .elementor-23
        .elementor-element.elementor-element-56b8a169 {
        left: -288px;
      }
      body.rtl .elementor-23 .elementor-element.elementor-element-56b8a169 {
        right: -288px;
      }
      .elementor-23 .elementor-element.elementor-element-877f259 {
        --spacer-size: 70px;
      }
      .elementor-23 .elementor-element.elementor-element-e84427b {
        text-align: center;
      }
      .elementor-23
        .elementor-element.elementor-element-e84427b
        .elementor-heading-title {
        color: #f6e9e4;
        font-family: "Allura", Sans-serif;
        font-size: 120px;
        font-weight: 300;
        line-height: 36px;
        letter-spacing: 2.1px;
      }
      .elementor-23
        .elementor-element.elementor-element-e84427b
        > .elementor-widget-container {
        margin: 10px 10px 10px 10px;
      }
      .elementor-23 .elementor-element.elementor-element-2de21ff {
        text-align: center;
      }
      .elementor-23
        .elementor-element.elementor-element-2de21ff
        .elementor-heading-title {
        font-weight: 100;
        -webkit-text-stroke-color: #000;
        stroke: #000;
        text-shadow: 0px 0px 0px rgba(0, 0, 0, 0.3);
      }
      .elementor-23
        .elementor-element.elementor-element-2de21ff
        > .elementor-widget-container {
        margin: -21px 0px 0px 0px;
      }
      .elementor-23 .elementor-element.elementor-element-82079ca {
        --divider-border-style: solid;
        --divider-color: #000;
        --divider-border-width: 1.7px;
      }
      .elementor-23
        .elementor-element.elementor-element-82079ca
        .elementor-divider-separator {
        width: 8%;
        margin: 0 auto;
        margin-center: 0;
      }
      .elementor-23
        .elementor-element.elementor-element-82079ca
        .elementor-divider {
        text-align: center;
        padding-top: 15px;
        padding-bottom: 15px;
      }
      .elementor-23 .elementor-element.elementor-element-0414084 {
        --spacer-size: 50px;
      }
      .elementor-23
        .elementor-element.elementor-element-573b176
        .heading-tbay-title {
        text-align: center;
      }
      .elementor-23 .elementor-element.elementor-element-573b176 .item-cat {
        text-align: center !important;
      }
      .elementor-23 .elementor-element.elementor-element-573b176 .content {
        text-align: center !important;
      }
      .elementor-23 .elementor-element.elementor-element-573b176 .cat-icon > i {
        font-size: 75px;
      }
      .elementor-23 .elementor-element.elementor-element-df0a954 {
        --spacer-size: 50px;
      }
      .elementor-23
        .elementor-element.elementor-element-26402fde:not(.elementor-motion-effects-element-type-background),
      .elementor-23
        .elementor-element.elementor-element-26402fde
        > .elementor-motion-effects-container
        > .elementor-motion-effects-layer {
        background-color: #2c2c2c;
      }
      .elementor-23 .elementor-element.elementor-element-26402fde {
        transition: background 0.3s, border 0.3s, border-radius 0.3s,
          box-shadow 0.3s;
      }
      .elementor-23
        .elementor-element.elementor-element-26402fde
        > .elementor-background-overlay {
        transition: background 0.3s, border-radius 0.3s, opacity 0.3s;
      }
      .elementor-23
        .elementor-element.elementor-element-1262f791:not(.elementor-motion-effects-element-type-background)
        > .elementor-widget-wrap,
      .elementor-23
        .elementor-element.elementor-element-1262f791
        > .elementor-widget-wrap
        > .elementor-motion-effects-container
        > .elementor-motion-effects-layer {
        background-color: #161010;
      }
      .elementor-23
        .elementor-element.elementor-element-1262f791
        > .elementor-element-populated {
        transition: background 0.3s, border 0.3s, border-radius 0.3s,
          box-shadow 0.3s;
      }
      .elementor-23
        .elementor-element.elementor-element-1262f791
        > .elementor-element-populated
        > .elementor-background-overlay {
        transition: background 0.3s, border-radius 0.3s, opacity 0.3s;
      }
      .elementor-23
        .elementor-element.elementor-element-6a60a5d
        .elementor-image-carousel-caption {
        text-align: center;
      }
      .elementor-23
        .elementor-element.elementor-element-604c79e1:not(.elementor-motion-effects-element-type-background),
      .elementor-23
        .elementor-element.elementor-element-604c79e1
        > .elementor-motion-effects-container
        > .elementor-motion-effects-layer {
        background-color: #2c2c2c;
      }
      .elementor-23 .elementor-element.elementor-element-604c79e1 {
        transition: background 0.3s, border 0.3s, border-radius 0.3s,
          box-shadow 0.3s;
      }
      .elementor-23
        .elementor-element.elementor-element-604c79e1
        > .elementor-background-overlay {
        transition: background 0.3s, border-radius 0.3s, opacity 0.3s;
      }
      .elementor-23
        .elementor-element.elementor-element-6c6c0f64
        > .elementor-element-populated {
        margin: 5px 5px 5px 5px;
        --e-column-margin-right: 5px;
        --e-column-margin-left: 5px;
      }
      .elementor-23 .elementor-element.elementor-element-2810f674 {
        text-align: center;
      }
      .elementor-23
        .elementor-element.elementor-element-2810f674
        .elementor-heading-title {
        color: #3c3c3c;
        font-family: "Allura", Sans-serif;
        font-size: 154px;
        font-weight: 100;
      }
      .elementor-23 .elementor-element.elementor-element-4809f91 {
        text-align: left;
        top: -34px;
      }
      .elementor-23
        .elementor-element.elementor-element-4809f91
        .elementor-heading-title {
        color: #ffffff;
        font-family: "cocosharp", Sans-serif;
        font-size: 41px;
        font-weight: 100;
      }
      .elementor-23
        .elementor-element.elementor-element-4809f91
        > .elementor-widget-container {
        margin: 0px 0px 0px 0px;
      }
      body:not(.rtl)
        .elementor-23
        .elementor-element.elementor-element-4809f91 {
        left: 69px;
      }
      body.rtl .elementor-23 .elementor-element.elementor-element-4809f91 {
        right: 69px;
      }
      .elementor-23 .elementor-element.elementor-element-80e0e47 {
        --divider-border-style: solid;
        --divider-color: #fbfbfb;
        --divider-border-width: 1.7px;
      }
      .elementor-23
        .elementor-element.elementor-element-80e0e47
        .elementor-divider-separator {
        width: 10%;
        margin: 0 auto;
        margin-center: 0;
      }
      .elementor-23
        .elementor-element.elementor-element-80e0e47
        .elementor-divider {
        text-align: center;
        padding-top: 20px;
        padding-bottom: 20px;
      }
      .elementor-23
        .elementor-element.elementor-element-3195d44c.elementor-view-stacked
        .elementor-icon {
        background-color: #ffffff;
      }
      .elementor-23
        .elementor-element.elementor-element-3195d44c.elementor-view-framed
        .elementor-icon,
      .elementor-23
        .elementor-element.elementor-element-3195d44c.elementor-view-default
        .elementor-icon {
        fill: #ffffff;
        color: #ffffff;
        border-color: #ffffff;
      }
      .elementor-23
        .elementor-element.elementor-element-3195d44c
        .elementor-icon-box-title {
        color: #fdfdfd;
      }
      .elementor-23
        .elementor-element.elementor-element-3195d44c
        .elementor-icon-box-title,
      .elementor-23
        .elementor-element.elementor-element-3195d44c
        .elementor-icon-box-title
        a {
        font-family: "cocosharp", Sans-serif;
        font-weight: 100;
        text-transform: uppercase;
        font-style: normal;
      }
      .elementor-23
        .elementor-element.elementor-element-3195d44c
        .elementor-icon-box-description {
        color: #ffffff;
      }
      .elementor-23
        .elementor-element.elementor-element-78b665e9.elementor-view-stacked
        .elementor-icon {
        background-color: #0c0c0c;
      }
      .elementor-23
        .elementor-element.elementor-element-78b665e9.elementor-view-framed
        .elementor-icon,
      .elementor-23
        .elementor-element.elementor-element-78b665e9.elementor-view-default
        .elementor-icon {
        fill: #0c0c0c;
        color: #0c0c0c;
        border-color: #0c0c0c;
      }
      .elementor-23
        .elementor-element.elementor-element-78b665e9
        .elementor-icon-box-title {
        color: #fdfdfd;
      }
      .elementor-23
        .elementor-element.elementor-element-78b665e9
        .elementor-icon-box-title,
      .elementor-23
        .elementor-element.elementor-element-78b665e9
        .elementor-icon-box-title
        a {
        font-family: "cocosharp", Sans-serif;
        font-weight: 100;
        text-transform: uppercase;
        font-style: normal;
      }
      .elementor-23
        .elementor-element.elementor-element-78b665e9
        .elementor-icon-box-description {
        color: #ffffff;
      }
      .elementor-23
        .elementor-element.elementor-element-57399bab.elementor-view-stacked
        .elementor-icon {
        background-color: #ffffff;
      }
      .elementor-23
        .elementor-element.elementor-element-57399bab.elementor-view-framed
        .elementor-icon,
      .elementor-23
        .elementor-element.elementor-element-57399bab.elementor-view-default
        .elementor-icon {
        fill: #ffffff;
        color: #ffffff;
        border-color: #ffffff;
      }
      .elementor-23
        .elementor-element.elementor-element-57399bab
        .elementor-icon-box-title {
        color: #fdfdfd;
      }
      .elementor-23
        .elementor-element.elementor-element-57399bab
        .elementor-icon-box-title,
      .elementor-23
        .elementor-element.elementor-element-57399bab
        .elementor-icon-box-title
        a {
        font-family: "cocosharp", Sans-serif;
        font-weight: 100;
        text-transform: uppercase;
        font-style: normal;
      }
      .elementor-23
        .elementor-element.elementor-element-57399bab
        .elementor-icon-box-description {
        color: #ffffff;
      }
      .elementor-23
        .elementor-element.elementor-element-3003f7ec.elementor-view-stacked
        .elementor-icon {
        background-color: #ffffff;
      }
      .elementor-23
        .elementor-element.elementor-element-3003f7ec.elementor-view-framed
        .elementor-icon,
      .elementor-23
        .elementor-element.elementor-element-3003f7ec.elementor-view-default
        .elementor-icon {
        fill: #ffffff;
        color: #ffffff;
        border-color: #ffffff;
      }
      .elementor-23
        .elementor-element.elementor-element-3003f7ec
        .elementor-icon-box-title {
        color: #fdfdfd;
      }
      .elementor-23
        .elementor-element.elementor-element-3003f7ec
        .elementor-icon-box-title,
      .elementor-23
        .elementor-element.elementor-element-3003f7ec
        .elementor-icon-box-title
        a {
        font-family: "cocosharp", Sans-serif;
        font-weight: 100;
        text-transform: uppercase;
        font-style: normal;
      }
      .elementor-23
        .elementor-element.elementor-element-3003f7ec
        .elementor-icon-box-description {
        color: #ffffff;
      }
      .elementor-23
        .elementor-element.elementor-element-57a339f7
        > .elementor-container
        > .elementor-column
        > .elementor-widget-wrap {
        align-content: center;
        align-items: center;
      }
      .elementor-23 .elementor-element.elementor-element-57a339f7 {
        overflow: hidden;
        transition: background 0.3s, border 0.3s, border-radius 0.3s,
          box-shadow 0.3s;
      }
      .elementor-23
        .elementor-element.elementor-element-57a339f7:not(.elementor-motion-effects-element-type-background),
      .elementor-23
        .elementor-element.elementor-element-57a339f7
        > .elementor-motion-effects-container
        > .elementor-motion-effects-layer {
        background-color: #2c2c2c;
      }
      .elementor-23
        .elementor-element.elementor-element-57a339f7
        > .elementor-background-overlay {
        transition: background 0.3s, border-radius 0.3s, opacity 0.3s;
      }
      .elementor-bc-flex-widget
        .elementor-23
        .elementor-element.elementor-element-373fb87d.elementor-column
        .elementor-widget-wrap {
        align-items: center;
      }
      .elementor-23
        .elementor-element.elementor-element-373fb87d.elementor-column.elementor-element[data-element_type="column"]
        > .elementor-widget-wrap.elementor-element-populated {
        align-content: center;
        align-items: center;
      }
      .elementor-23
        .elementor-element.elementor-element-373fb87d.elementor-column
        > .elementor-widget-wrap {
        justify-content: center;
      }
      .elementor-23
        .elementor-element.elementor-element-373fb87d:not(.elementor-motion-effects-element-type-background)
        > .elementor-widget-wrap,
      .elementor-23
        .elementor-element.elementor-element-373fb87d
        > .elementor-widget-wrap
        > .elementor-motion-effects-container
        > .elementor-motion-effects-layer {
        background-image: url("https://zibabeauty.in/wp-content/uploads/2023/04/overhead-view-makeup-products-with-brushes-black-backdrop-scaled-1.jpg");
        background-position: center center;
        background-repeat: no-repeat;
        background-size: cover;
      }
      .elementor-23
        .elementor-element.elementor-element-373fb87d
        > .elementor-element-populated {
        transition: background 0.3s, border 0.3s, border-radius 0.3s,
          box-shadow 0.3s;
      }
      .elementor-23
        .elementor-element.elementor-element-373fb87d
        > .elementor-element-populated
        > .elementor-background-overlay {
        transition: background 0.3s, border-radius 0.3s, opacity 0.3s;
      }
      .elementor-23 .elementor-element.elementor-element-26b15076 {
        --spacer-size: 50px;
      }
      .elementor-23
        .elementor-element.elementor-element-62d2ac28:not(.elementor-motion-effects-element-type-background)
        > .elementor-widget-wrap,
      .elementor-23
        .elementor-element.elementor-element-62d2ac28
        > .elementor-widget-wrap
        > .elementor-motion-effects-container
        > .elementor-motion-effects-layer {
        background-color: #2c2c2c;
      }
      .elementor-23
        .elementor-element.elementor-element-62d2ac28
        > .elementor-element-populated {
        transition: background 0.3s, border 0.3s, border-radius 0.3s,
          box-shadow 0.3s;
        margin: 20px 20px 20px 20px;
        --e-column-margin-right: 20px;
        --e-column-margin-left: 20px;
      }
      .elementor-23
        .elementor-element.elementor-element-62d2ac28
        > .elementor-element-populated
        > .elementor-background-overlay {
        transition: background 0.3s, border-radius 0.3s, opacity 0.3s;
      }
      .elementor-23 .elementor-element.elementor-element-3ad85a42 {
        text-align: left;
      }
      .elementor-23
        .elementor-element.elementor-element-3ad85a42
        .elementor-heading-title {
        color: #3c3c3c;
        font-family: "Allura", Sans-serif;
        font-size: 154px;
        font-weight: 100;
      }
      .elementor-23
        .elementor-element.elementor-element-3ad85a42
        > .elementor-widget-container {
        margin: 0px 0px 0px 020px;
      }
      .elementor-23 .elementor-element.elementor-element-6f1279fa {
        --spacer-size: 50px;
      }
      .elementor-23 .elementor-element.elementor-element-16bad9ca {
        text-align: center;
        top: 80px;
      }
      .elementor-23
        .elementor-element.elementor-element-16bad9ca
        .elementor-heading-title {
        color: #ffffff;
        font-family: "cocosharp", Sans-serif;
        font-size: 47px;
        font-weight: 100;
      }
      .elementor-23
        .elementor-element.elementor-element-16bad9ca
        > .elementor-widget-container {
        margin: 0px 0px 0px 05px;
      }
      body:not(.rtl)
        .elementor-23
        .elementor-element.elementor-element-16bad9ca {
        left: 0px;
      }
      body.rtl .elementor-23 .elementor-element.elementor-element-16bad9ca {
        right: 0px;
      }
      .elementor-23 .elementor-element.elementor-element-6af49e63 {
        --divider-border-style: solid;
        --divider-color: #fbfbfb;
        --divider-border-width: 2.6px;
      }
      .elementor-23
        .elementor-element.elementor-element-6af49e63
        .elementor-divider-separator {
        width: 10%;
        margin: 0 auto;
        margin-center: 0;
      }
      .elementor-23
        .elementor-element.elementor-element-6af49e63
        .elementor-divider {
        text-align: center;
        padding-top: 20px;
        padding-bottom: 20px;
      }
      .elementor-23
        .elementor-element.elementor-element-31c9c1fa.elementor-view-stacked
        .elementor-icon {
        background-color: #ffffff;
      }
      .elementor-23
        .elementor-element.elementor-element-31c9c1fa.elementor-view-framed
        .elementor-icon,
      .elementor-23
        .elementor-element.elementor-element-31c9c1fa.elementor-view-default
        .elementor-icon {
        fill: #ffffff;
        color: #ffffff;
        border-color: #ffffff;
      }
      .elementor-23
        .elementor-element.elementor-element-31c9c1fa
        .elementor-icon-box-title {
        color: #fdfdfd;
      }
      .elementor-23
        .elementor-element.elementor-element-31c9c1fa
        .elementor-icon-box-title,
      .elementor-23
        .elementor-element.elementor-element-31c9c1fa
        .elementor-icon-box-title
        a {
        font-family: "cocosharp", Sans-serif;
        font-weight: 100;
        text-transform: uppercase;
        font-style: normal;
      }
      .elementor-23
        .elementor-element.elementor-element-31c9c1fa
        .elementor-icon-box-description {
        color: #ffffff;
      }
      .elementor-23
        .elementor-element.elementor-element-31c9c1fa
        > .elementor-widget-container {
        margin: 20px 20px 20px 20px;
      }
      .elementor-23
        .elementor-element.elementor-element-2c214dca.elementor-view-stacked
        .elementor-icon {
        background-color: #0c0c0c;
      }
      .elementor-23
        .elementor-element.elementor-element-2c214dca.elementor-view-framed
        .elementor-icon,
      .elementor-23
        .elementor-element.elementor-element-2c214dca.elementor-view-default
        .elementor-icon {
        fill: #0c0c0c;
        color: #0c0c0c;
        border-color: #0c0c0c;
      }
      .elementor-23
        .elementor-element.elementor-element-2c214dca
        .elementor-icon-box-title {
        color: #fdfdfd;
      }
      .elementor-23
        .elementor-element.elementor-element-2c214dca
        .elementor-icon-box-title,
      .elementor-23
        .elementor-element.elementor-element-2c214dca
        .elementor-icon-box-title
        a {
        font-family: "cocosharp", Sans-serif;
        font-weight: 100;
        text-transform: uppercase;
        font-style: normal;
      }
      .elementor-23
        .elementor-element.elementor-element-2c214dca
        .elementor-icon-box-description {
        color: #ffffff;
      }
      .elementor-23
        .elementor-element.elementor-element-2c214dca
        > .elementor-widget-container {
        margin: 20px 20px 20px 20px;
      }
      .elementor-23
        .elementor-element.elementor-element-5daae136.elementor-view-stacked
        .elementor-icon {
        background-color: #ffffff;
      }
      .elementor-23
        .elementor-element.elementor-element-5daae136.elementor-view-framed
        .elementor-icon,
      .elementor-23
        .elementor-element.elementor-element-5daae136.elementor-view-default
        .elementor-icon {
        fill: #ffffff;
        color: #ffffff;
        border-color: #ffffff;
      }
      .elementor-23
        .elementor-element.elementor-element-5daae136
        .elementor-icon-box-title {
        color: #fdfdfd;
      }
      .elementor-23
        .elementor-element.elementor-element-5daae136
        .elementor-icon-box-title,
      .elementor-23
        .elementor-element.elementor-element-5daae136
        .elementor-icon-box-title
        a {
        font-family: "cocosharp", Sans-serif;
        font-weight: 100;
        text-transform: uppercase;
        font-style: normal;
      }
      .elementor-23
        .elementor-element.elementor-element-5daae136
        .elementor-icon-box-description {
        color: #ffffff;
      }
      .elementor-23
        .elementor-element.elementor-element-5daae136
        > .elementor-widget-container {
        margin: 20px 20px 20px 20px;
      }
      .elementor-23 .elementor-element.elementor-element-135498a5 {
        --spacer-size: 50px;
      }
      .elementor-23
        .elementor-element.elementor-element-130c3e4b.elementor-view-stacked
        .elementor-icon {
        background-color: #ffffff;
      }
      .elementor-23
        .elementor-element.elementor-element-130c3e4b.elementor-view-framed
        .elementor-icon,
      .elementor-23
        .elementor-element.elementor-element-130c3e4b.elementor-view-default
        .elementor-icon {
        fill: #ffffff;
        color: #ffffff;
        border-color: #ffffff;
      }
      .elementor-23
        .elementor-element.elementor-element-130c3e4b
        .elementor-icon-box-title {
        color: #fdfdfd;
      }
      .elementor-23
        .elementor-element.elementor-element-130c3e4b
        .elementor-icon-box-title,
      .elementor-23
        .elementor-element.elementor-element-130c3e4b
        .elementor-icon-box-title
        a {
        font-family: "cocosharp", Sans-serif;
        font-weight: 100;
        text-transform: uppercase;
        font-style: normal;
      }
      .elementor-23
        .elementor-element.elementor-element-130c3e4b
        .elementor-icon-box-description {
        color: #ffffff;
      }
      .elementor-23
        .elementor-element.elementor-element-130c3e4b
        > .elementor-widget-container {
        margin: 20px 20px 20px 20px;
      }
      .elementor-23 .elementor-element.elementor-element-6cc09fc5 {
        --spacer-size: 50px;
      }
      body:not(.rtl)
        .elementor-23
        .elementor-element.elementor-element-84b5cc6 {
        left: 591px;
      }
      body.rtl .elementor-23 .elementor-element.elementor-element-84b5cc6 {
        right: 591px;
      }
      .elementor-23 .elementor-element.elementor-element-84b5cc6 {
        top: 0px;
      }
      body:not(.rtl)
        .elementor-23
        .elementor-element.elementor-element-4f5c91df {
        left: 591px;
      }
      body.rtl .elementor-23 .elementor-element.elementor-element-4f5c91df {
        right: 591px;
      }
      .elementor-23 .elementor-element.elementor-element-4f5c91df {
        top: 0px;
      }
      .elementor-23 .elementor-element.elementor-element-2f8d00a4 {
        transition: background 0.3s, border 0.3s, border-radius 0.3s,
          box-shadow 0.3s;
        padding: 43px 0px 15px 0px;
      }
      .elementor-23
        .elementor-element.elementor-element-2f8d00a4
        > .elementor-background-overlay {
        transition: background 0.3s, border-radius 0.3s, opacity 0.3s;
      }
      .elementor-23
        .elementor-element.elementor-element-1378be25
        > .elementor-element-populated {
        transition: background 0.3s, border 0.3s, border-radius 0.3s,
          box-shadow 0.3s;
        margin: 5px 5px 5px 5px;
        --e-column-margin-right: 5px;
        --e-column-margin-left: 5px;
      }
      .elementor-23
        .elementor-element.elementor-element-1378be25
        > .elementor-element-populated
        > .elementor-background-overlay {
        transition: background 0.3s, border-radius 0.3s, opacity 0.3s;
      }
      .elementor-23 .elementor-element.elementor-element-5ee330ba {
        text-align: center;
      }
      .elementor-23
        .elementor-element.elementor-element-5ee330ba
        .elementor-heading-title {
        color: #f6e9e4;
        font-family: "Ephesis", Sans-serif;
        font-size: 120px;
        font-weight: 300;
      }
      .elementor-23 .elementor-element.elementor-element-215608c9 {
        text-align: center;
        top: 81px;
      }
      .elementor-23
        .elementor-element.elementor-element-215608c9
        .elementor-heading-title {
        color: #000000;
        font-family: "cocosharp", Sans-serif;
        font-size: 48px;
        font-weight: 300;
        letter-spacing: 1.8px;
      }
      body:not(.rtl)
        .elementor-23
        .elementor-element.elementor-element-215608c9 {
        left: -21px;
      }
      body.rtl .elementor-23 .elementor-element.elementor-element-215608c9 {
        right: -21px;
      }
      .elementor-23 .elementor-element.elementor-element-9ca8e67 {
        --divider-border-style: solid;
        --divider-color: #000;
        --divider-border-width: 1.7px;
      }
      .elementor-23
        .elementor-element.elementor-element-9ca8e67
        .elementor-divider-separator {
        width: 8%;
        margin: 0 auto;
        margin-center: 0;
      }
      .elementor-23
        .elementor-element.elementor-element-9ca8e67
        .elementor-divider {
        text-align: center;
        padding-top: 15px;
        padding-bottom: 15px;
      }
      .elementor-23 .elementor-element.elementor-element-d5ce1b0 {
        --spacer-size: 50px;
      }
      .elementor-23
        .elementor-element.elementor-element-ca5886a
        .heading-tbay-title {
        text-align: center;
      }
      .elementor-23
        .elementor-element.elementor-element-ec8e085
        .elementor-button {
        font-weight: 300;
        text-transform: uppercase;
        fill: #2c2c2c;
        color: #2c2c2c;
        background-color: #ffffff;
        border-style: solid;
      }
      .elementor-23
        .elementor-element.elementor-element-ec8e085
        .elementor-button:hover,
      .elementor-23
        .elementor-element.elementor-element-ec8e085
        .elementor-button:focus {
        color: #fdfdfd;
        background-color: #2c2c2c;
      }
      .elementor-23
        .elementor-element.elementor-element-ec8e085
        .elementor-button:hover
        svg,
      .elementor-23
        .elementor-element.elementor-element-ec8e085
        .elementor-button:focus
        svg {
        fill: #fdfdfd;
      }
      .elementor-23 .elementor-element.elementor-element-180780ca {
        transition: background 0.3s, border 0.3s, border-radius 0.3s,
          box-shadow 0.3s;
        padding: 43px 0px 15px 0px;
      }
      .elementor-23
        .elementor-element.elementor-element-180780ca
        > .elementor-background-overlay {
        transition: background 0.3s, border-radius 0.3s, opacity 0.3s;
      }
      .elementor-23
        .elementor-element.elementor-element-2dc9198b
        > .elementor-element-populated {
        transition: background 0.3s, border 0.3s, border-radius 0.3s,
          box-shadow 0.3s;
        margin: 5px 5px 5px 5px;
        --e-column-margin-right: 5px;
        --e-column-margin-left: 5px;
      }
      .elementor-23
        .elementor-element.elementor-element-2dc9198b
        > .elementor-element-populated
        > .elementor-background-overlay {
        transition: background 0.3s, border-radius 0.3s, opacity 0.3s;
      }
      .elementor-23 .elementor-element.elementor-element-4cee084f {
        text-align: center;
      }
      .elementor-23
        .elementor-element.elementor-element-4cee084f
        .elementor-heading-title {
        color: #f6e9e4;
        font-family: "Ephesis", Sans-serif;
        font-size: 120px;
        font-weight: 300;
      }
      .elementor-23 .elementor-element.elementor-element-231ea8c8 {
        text-align: center;
        top: 81px;
      }
      .elementor-23
        .elementor-element.elementor-element-231ea8c8
        .elementor-heading-title {
        color: #000000;
        font-family: "cocosharp", Sans-serif;
        font-size: 48px;
        font-weight: 300;
        letter-spacing: 1.8px;
      }
      body:not(.rtl)
        .elementor-23
        .elementor-element.elementor-element-231ea8c8 {
        left: -21px;
      }
      body.rtl .elementor-23 .elementor-element.elementor-element-231ea8c8 {
        right: -21px;
      }
      .elementor-23 .elementor-element.elementor-element-e351b02 {
        --divider-border-style: solid;
        --divider-color: #000;
        --divider-border-width: 1.7px;
      }
      .elementor-23
        .elementor-element.elementor-element-e351b02
        .elementor-divider-separator {
        width: 8%;
        margin: 0 auto;
        margin-center: 0;
      }
      .elementor-23
        .elementor-element.elementor-element-e351b02
        .elementor-divider {
        text-align: center;
        padding-top: 15px;
        padding-bottom: 15px;
      }
      .elementor-23 .elementor-element.elementor-element-03123d6 {
        --spacer-size: 50px;
      }
      .elementor-23
        .elementor-element.elementor-element-d603d2d
        .heading-tbay-title {
        text-align: center;
      }
      .elementor-23
        .elementor-element.elementor-element-f478f5c
        .elementor-button {
        font-weight: 300;
        text-transform: uppercase;
        fill: #2c2c2c;
        color: #2c2c2c;
        background-color: #ffffff;
        border-style: solid;
      }
      .elementor-23
        .elementor-element.elementor-element-f478f5c
        .elementor-button:hover,
      .elementor-23
        .elementor-element.elementor-element-f478f5c
        .elementor-button:focus {
        color: #fdfdfd;
        background-color: #2c2c2c;
      }
      .elementor-23
        .elementor-element.elementor-element-f478f5c
        .elementor-button:hover
        svg,
      .elementor-23
        .elementor-element.elementor-element-f478f5c
        .elementor-button:focus
        svg {
        fill: #fdfdfd;
      }
      .elementor-23 .elementor-element.elementor-element-3feecfe {
        transition: background 0.3s, border 0.3s, border-radius 0.3s,
          box-shadow 0.3s;
        padding: 43px 0px 15px 0px;
      }
      .elementor-23
        .elementor-element.elementor-element-3feecfe
        > .elementor-background-overlay {
        transition: background 0.3s, border-radius 0.3s, opacity 0.3s;
      }
      .elementor-23
        .elementor-element.elementor-element-99f0cf8
        > .elementor-element-populated {
        transition: background 0.3s, border 0.3s, border-radius 0.3s,
          box-shadow 0.3s;
        margin: 5px 5px 5px 5px;
        --e-column-margin-right: 5px;
        --e-column-margin-left: 5px;
      }
      .elementor-23
        .elementor-element.elementor-element-99f0cf8
        > .elementor-element-populated
        > .elementor-background-overlay {
        transition: background 0.3s, border-radius 0.3s, opacity 0.3s;
      }
      .elementor-23 .elementor-element.elementor-element-85eca84 {
        text-align: center;
      }
      .elementor-23
        .elementor-element.elementor-element-85eca84
        .elementor-heading-title {
        color: #f6e9e4;
        font-family: "Ephesis", Sans-serif;
        font-size: 120px;
        font-weight: 300;
      }
      .elementor-23 .elementor-element.elementor-element-9093ef1 {
        text-align: center;
        top: 81px;
      }
      .elementor-23
        .elementor-element.elementor-element-9093ef1
        .elementor-heading-title {
        color: #000000;
        font-family: "cocosharp", Sans-serif;
        font-size: 48px;
        font-weight: 300;
        letter-spacing: 1.8px;
      }
      body:not(.rtl)
        .elementor-23
        .elementor-element.elementor-element-9093ef1 {
        left: -21px;
      }
      body.rtl .elementor-23 .elementor-element.elementor-element-9093ef1 {
        right: -21px;
      }
      .elementor-23 .elementor-element.elementor-element-613bece {
        --divider-border-style: solid;
        --divider-color: #000;
        --divider-border-width: 1.7px;
      }
      .elementor-23
        .elementor-element.elementor-element-613bece
        .elementor-divider-separator {
        width: 8%;
        margin: 0 auto;
        margin-center: 0;
      }
      .elementor-23
        .elementor-element.elementor-element-613bece
        .elementor-divider {
        text-align: center;
        padding-top: 15px;
        padding-bottom: 15px;
      }
      .elementor-23 .elementor-element.elementor-element-be4c1c5 {
        --spacer-size: 50px;
      }
      .elementor-23
        .elementor-element.elementor-element-fb2b4c4
        .heading-tbay-title {
        text-align: center;
      }
      .elementor-23
        .elementor-element.elementor-element-5a06225
        .elementor-button {
        font-weight: 400;
        text-transform: uppercase;
        fill: #2c2c2c;
        color: #2c2c2c;
        background-color: #ffffff;
        border-style: solid;
      }
      .elementor-23
        .elementor-element.elementor-element-5a06225
        .elementor-button:hover,
      .elementor-23
        .elementor-element.elementor-element-5a06225
        .elementor-button:focus {
        color: #fdfdfd;
        background-color: #2c2c2c;
      }
      .elementor-23
        .elementor-element.elementor-element-5a06225
        .elementor-button:hover
        svg,
      .elementor-23
        .elementor-element.elementor-element-5a06225
        .elementor-button:focus
        svg {
        fill: #fdfdfd;
      }
      .elementor-23 .elementor-element.elementor-element-a1c33cc {
        transition: background 0.3s, border 0.3s, border-radius 0.3s,
          box-shadow 0.3s;
        padding: 43px 0px 15px 0px;
      }
      .elementor-23
        .elementor-element.elementor-element-a1c33cc
        > .elementor-background-overlay {
        transition: background 0.3s, border-radius 0.3s, opacity 0.3s;
      }
      .elementor-23
        .elementor-element.elementor-element-16eebdc
        > .elementor-element-populated {
        transition: background 0.3s, border 0.3s, border-radius 0.3s,
          box-shadow 0.3s;
        margin: 5px 5px 5px 5px;
        --e-column-margin-right: 5px;
        --e-column-margin-left: 5px;
      }
      .elementor-23
        .elementor-element.elementor-element-16eebdc
        > .elementor-element-populated
        > .elementor-background-overlay {
        transition: background 0.3s, border-radius 0.3s, opacity 0.3s;
      }
      .elementor-23 .elementor-element.elementor-element-f5dfa08 {
        text-align: center;
      }
      .elementor-23
        .elementor-element.elementor-element-f5dfa08
        .elementor-heading-title {
        color: #f6e9e4;
        font-family: "Ephesis", Sans-serif;
        font-size: 120px;
        font-weight: 300;
      }
      .elementor-23 .elementor-element.elementor-element-f94cdc3 {
        text-align: center;
        top: 81px;
      }
      .elementor-23
        .elementor-element.elementor-element-f94cdc3
        .elementor-heading-title {
        color: #000000;
        font-family: "cocosharp", Sans-serif;
        font-size: 48px;
        font-weight: 300;
        letter-spacing: 1.8px;
      }
      body:not(.rtl)
        .elementor-23
        .elementor-element.elementor-element-f94cdc3 {
        left: -21px;
      }
      body.rtl .elementor-23 .elementor-element.elementor-element-f94cdc3 {
        right: -21px;
      }
      .elementor-23 .elementor-element.elementor-element-c812a1d {
        --divider-border-style: solid;
        --divider-color: #000;
        --divider-border-width: 1.7px;
      }
      .elementor-23
        .elementor-element.elementor-element-c812a1d
        .elementor-divider-separator {
        width: 8%;
        margin: 0 auto;
        margin-center: 0;
      }
      .elementor-23
        .elementor-element.elementor-element-c812a1d
        .elementor-divider {
        text-align: center;
        padding-top: 15px;
        padding-bottom: 15px;
      }
      .elementor-23 .elementor-element.elementor-element-932e240 {
        --spacer-size: 50px;
      }
      .elementor-23
        .elementor-element.elementor-element-0bf4754
        .heading-tbay-title {
        text-align: center;
      }
      .elementor-23
        .elementor-element.elementor-element-7f8a6c4
        .elementor-button {
        font-weight: 400;
        text-transform: uppercase;
        fill: #2c2c2c;
        color: #2c2c2c;
        background-color: #ffffff;
        border-style: solid;
      }
      .elementor-23
        .elementor-element.elementor-element-7f8a6c4
        .elementor-button:hover,
      .elementor-23
        .elementor-element.elementor-element-7f8a6c4
        .elementor-button:focus {
        color: #fdfdfd;
        background-color: #2c2c2c;
      }
      .elementor-23
        .elementor-element.elementor-element-7f8a6c4
        .elementor-button:hover
        svg,
      .elementor-23
        .elementor-element.elementor-element-7f8a6c4
        .elementor-button:focus
        svg {
        fill: #fdfdfd;
      }
      .elementor-23 .elementor-element.elementor-element-dcb7b30 {
        --spacer-size: 50px;
      }
      .elementor-23 .elementor-element.elementor-element-3900625d {
        overflow: hidden;
      }
      .elementor-bc-flex-widget
        .elementor-23
        .elementor-element.elementor-element-137a3f18.elementor-column
        .elementor-widget-wrap {
        align-items: flex-start;
      }
      .elementor-23
        .elementor-element.elementor-element-137a3f18.elementor-column.elementor-element[data-element_type="column"]
        > .elementor-widget-wrap.elementor-element-populated {
        align-content: flex-start;
        align-items: flex-start;
      }
      .elementor-23
        .elementor-element.elementor-element-137a3f18.elementor-column
        > .elementor-widget-wrap {
        justify-content: center;
      }
      .elementor-23
        .elementor-element.elementor-element-137a3f18:not(.elementor-motion-effects-element-type-background)
        > .elementor-widget-wrap,
      .elementor-23
        .elementor-element.elementor-element-137a3f18
        > .elementor-widget-wrap
        > .elementor-motion-effects-container
        > .elementor-motion-effects-layer {
        background-image: url("https://zibabeauty.in/wp-content/uploads/2023/04/BHP_0653.jpg");
        background-position: center center;
        background-repeat: no-repeat;
        background-size: cover;
      }
      .elementor-23
        .elementor-element.elementor-element-137a3f18
        > .elementor-element-populated {
        transition: background 0.3s, border 0.3s, border-radius 0.3s,
          box-shadow 0.3s;
      }
      .elementor-23
        .elementor-element.elementor-element-137a3f18
        > .elementor-element-populated
        > .elementor-background-overlay {
        transition: background 0.3s, border-radius 0.3s, opacity 0.3s;
      }
      .elementor-23 .elementor-element.elementor-element-dce8588 {
        text-align: left;
      }
      .elementor-23
        .elementor-element.elementor-element-dce8588
        .elementor-heading-title {
        color: #f6e9e4;
        font-family: "Ephesis", Sans-serif;
        font-size: 162px;
        font-weight: 100;
      }
      .elementor-23 .elementor-element.elementor-element-3b10034e {
        text-align: left;
      }
      .elementor-23
        .elementor-element.elementor-element-3b10034e
        .elementor-heading-title {
        color: #9f593e;
        font-family: "cocosharp", Sans-serif;
        font-size: 35px;
        font-weight: 300;
      }
      .elementor-23
        .elementor-element.elementor-element-3b10034e
        > .elementor-widget-container {
        margin: -63px 0px 0px 47px;
      }
      .elementor-23 .elementor-element.elementor-element-5d749faa {
        --spacer-size: 50px;
      }
      .elementor-23 .elementor-element.elementor-element-215962dc {
        text-align: center;
      }
      .elementor-bc-flex-widget
        .elementor-23
        .elementor-element.elementor-element-64a63cf.elementor-column
        .elementor-widget-wrap {
        align-items: flex-start;
      }
      .elementor-23
        .elementor-element.elementor-element-64a63cf.elementor-column.elementor-element[data-element_type="column"]
        > .elementor-widget-wrap.elementor-element-populated {
        align-content: flex-start;
        align-items: flex-start;
      }
      .elementor-23
        .elementor-element.elementor-element-64a63cf.elementor-column
        > .elementor-widget-wrap {
        justify-content: center;
      }
      .elementor-23
        .elementor-element.elementor-element-64a63cf:not(.elementor-motion-effects-element-type-background)
        > .elementor-widget-wrap,
      .elementor-23
        .elementor-element.elementor-element-64a63cf
        > .elementor-widget-wrap
        > .elementor-motion-effects-container
        > .elementor-motion-effects-layer {
        background-image: url("https://zibabeauty.in/wp-content/uploads/2023/04/BHP_0691.jpg");
        background-position: top center;
        background-repeat: no-repeat;
        background-size: cover;
      }
      .elementor-23
        .elementor-element.elementor-element-64a63cf
        > .elementor-element-populated {
        transition: background 0.3s, border 0.3s, border-radius 0.3s,
          box-shadow 0.3s;
        margin: 0px 0px 0px 0px;
        --e-column-margin-right: 0px;
        --e-column-margin-left: 0px;
      }
      .elementor-23
        .elementor-element.elementor-element-64a63cf
        > .elementor-element-populated
        > .elementor-background-overlay {
        transition: background 0.3s, border-radius 0.3s, opacity 0.3s;
      }
      .elementor-23 .elementor-element.elementor-element-17a23b7d {
        text-align: left;
      }
      .elementor-23
        .elementor-element.elementor-element-17a23b7d
        .elementor-heading-title {
        color: #fbf4f2;
        font-family: "Ephesis", Sans-serif;
        font-size: 154px;
        font-weight: 100;
      }
      .elementor-23 .elementor-element.elementor-element-5e23f225 {
        text-align: left;
      }
      .elementor-23
        .elementor-element.elementor-element-5e23f225
        .elementor-heading-title {
        color: #9f593e;
        font-family: "cocosharp", Sans-serif;
        font-size: 48px;
        font-weight: 300;
      }
      .elementor-23
        .elementor-element.elementor-element-5e23f225
        > .elementor-widget-container {
        margin: -63px 0px 0px 47px;
      }
      .elementor-23 .elementor-element.elementor-element-270abd44 {
        --spacer-size: 50px;
      }
      .elementor-23 .elementor-element.elementor-element-5044eaa5 {
        text-align: center;
        color: #000000;
      }
      .elementor-23
        .elementor-element.elementor-element-5044eaa5
        > .elementor-widget-container {
        margin: 20px 20px 20px 20px;
      }
      .elementor-23 .elementor-element.elementor-element-5102b149 {
        --spacer-size: 50px;
      }
      .elementor-23
        .elementor-element.elementor-element-3828aea0
        > .elementor-container
        > .elementor-column
        > .elementor-widget-wrap {
        align-content: center;
        align-items: center;
      }
      .elementor-23 .elementor-element.elementor-element-3828aea0 {
        overflow: hidden;
        transition: background 0.3s, border 0.3s, border-radius 0.3s,
          box-shadow 0.3s;
      }
      .elementor-23
        .elementor-element.elementor-element-3828aea0:not(.elementor-motion-effects-element-type-background),
      .elementor-23
        .elementor-element.elementor-element-3828aea0
        > .elementor-motion-effects-container
        > .elementor-motion-effects-layer {
        background-color: #fcf1ef;
      }
      .elementor-23
        .elementor-element.elementor-element-3828aea0
        > .elementor-background-overlay {
        transition: background 0.3s, border-radius 0.3s, opacity 0.3s;
      }
      .elementor-bc-flex-widget
        .elementor-23
        .elementor-element.elementor-element-3dde861f.elementor-column
        .elementor-widget-wrap {
        align-items: center;
      }
      .elementor-23
        .elementor-element.elementor-element-3dde861f.elementor-column.elementor-element[data-element_type="column"]
        > .elementor-widget-wrap.elementor-element-populated {
        align-content: center;
        align-items: center;
      }
      .elementor-23
        .elementor-element.elementor-element-3dde861f.elementor-column
        > .elementor-widget-wrap {
        justify-content: center;
      }
      .elementor-23
        .elementor-element.elementor-element-3dde861f
        > .elementor-widget-wrap
        > .elementor-widget:not(.elementor-widget__width-auto):not(.elementor-widget__width-initial):not(:last-child):not(.elementor-absolute) {
        margin-bottom: 0px;
      }
      .elementor-23
        .elementor-element.elementor-element-3dde861f:not(.elementor-motion-effects-element-type-background)
        > .elementor-widget-wrap,
      .elementor-23
        .elementor-element.elementor-element-3dde861f
        > .elementor-widget-wrap
        > .elementor-motion-effects-container
        > .elementor-motion-effects-layer {
        background-color: #fcf1ef;
        background-image: url("https://zibabeauty.in/wp-content/uploads/2023/04/front-view-charming-female-model-blue-dress-studio-shot-attractive-pinup-girl-looking-camera.png");
        background-position: center center;
        background-repeat: no-repeat;
        background-size: cover;
      }
      .elementor-23
        .elementor-element.elementor-element-3dde861f
        > .elementor-element-populated {
        transition: background 0.3s, border 0.3s, border-radius 0.3s,
          box-shadow 0.3s;
      }
      .elementor-23
        .elementor-element.elementor-element-3dde861f
        > .elementor-element-populated
        > .elementor-background-overlay {
        transition: background 0.3s, border-radius 0.3s, opacity 0.3s;
      }
      .elementor-23 .elementor-element.elementor-element-3dd7e092 {
        --spacer-size: 600px;
      }
      .elementor-23
        .elementor-element.elementor-element-32bc5957
        > .elementor-element-populated {
        margin: 5px 5px 5px 5px;
        --e-column-margin-right: 5px;
        --e-column-margin-left: 5px;
      }
      .elementor-23 .elementor-element.elementor-element-49871dee {
        text-align: center;
      }
      .elementor-23
        .elementor-element.elementor-element-49871dee
        .elementor-heading-title {
        color: #ffffff;
        font-family: "Allura", Sans-serif;
        font-size: 127px;
        font-weight: 100;
      }
      .elementor-23 .elementor-element.elementor-element-79cd7484 {
        text-align: center;
      }
      .elementor-23
        .elementor-element.elementor-element-79cd7484
        .elementor-heading-title {
        color: #232323;
        font-family: "cocosharp", Sans-serif;
        font-size: 43px;
        font-weight: 100;
        font-style: normal;
      }
      .elementor-23
        .elementor-element.elementor-element-79cd7484
        > .elementor-widget-container {
        margin: -63px -63px -63px -63px;
      }
      .elementor-23 .elementor-element.elementor-element-5e232fd4 {
        --divider-border-style: solid;
        --divider-color: #101010;
        --divider-border-width: 1.7px;
      }
      .elementor-23
        .elementor-element.elementor-element-5e232fd4
        .elementor-divider-separator {
        width: 10%;
        margin: 0 auto;
        margin-center: 0;
      }
      .elementor-23
        .elementor-element.elementor-element-5e232fd4
        .elementor-divider {
        text-align: center;
        padding-top: 20px;
        padding-bottom: 20px;
      }
      .elementor-23
        .elementor-element.elementor-element-f9cc1bd
        .heading-tbay-title {
        text-align: center;
      }
      .elementor-23
        .elementor-element.elementor-element-39aecc2a
        > .elementor-container
        > .elementor-column
        > .elementor-widget-wrap {
        align-content: flex-start;
        align-items: flex-start;
      }
      .elementor-23
        .elementor-element.elementor-element-39aecc2a:not(.elementor-motion-effects-element-type-background),
      .elementor-23
        .elementor-element.elementor-element-39aecc2a
        > .elementor-motion-effects-container
        > .elementor-motion-effects-layer {
        background-color: #fcf1ef;
      }
      .elementor-23 .elementor-element.elementor-element-39aecc2a {
        transition: background 0.3s, border 0.3s, border-radius 0.3s,
          box-shadow 0.3s;
      }
      .elementor-23
        .elementor-element.elementor-element-39aecc2a
        > .elementor-background-overlay {
        transition: background 0.3s, border-radius 0.3s, opacity 0.3s;
      }
      .elementor-23
        .elementor-element.elementor-element-5164bc9d
        > .elementor-element-populated {
        margin: 5px 5px 5px 5px;
        --e-column-margin-right: 5px;
        --e-column-margin-left: 5px;
      }
      .elementor-23 .elementor-element.elementor-element-1f2e84af {
        text-align: left;
      }
      .elementor-23
        .elementor-element.elementor-element-1f2e84af
        .elementor-heading-title {
        color: #f6e9e4;
        font-family: "Allura", Sans-serif;
        font-size: 127px;
        font-weight: 100;
      }
      .elementor-23 .elementor-element.elementor-element-7d8c2138 {
        text-align: center;
        top: 105px;
      }
      .elementor-23
        .elementor-element.elementor-element-7d8c2138
        .elementor-heading-title {
        color: #232323;
        font-family: "cocosharp", Sans-serif;
        font-size: 43px;
        font-weight: 100;
        font-style: normal;
      }
      .elementor-23
        .elementor-element.elementor-element-7d8c2138
        > .elementor-widget-container {
        margin: -63px -63px -63px -63px;
      }
      body:not(.rtl)
        .elementor-23
        .elementor-element.elementor-element-7d8c2138 {
        left: -190px;
      }
      body.rtl .elementor-23 .elementor-element.elementor-element-7d8c2138 {
        right: -190px;
      }
      .elementor-23 .elementor-element.elementor-element-78ec5763 {
        --divider-border-style: solid;
        --divider-color: #101010;
        --divider-border-width: 1.7px;
      }
      .elementor-23
        .elementor-element.elementor-element-78ec5763
        .elementor-divider-separator {
        width: 10%;
        margin: 0 auto;
        margin-center: 0;
      }
      .elementor-23
        .elementor-element.elementor-element-78ec5763
        .elementor-divider {
        text-align: center;
        padding-top: 20px;
        padding-bottom: 20px;
      }
      .elementor-23
        .elementor-element.elementor-element-b786c90
        .heading-tbay-title {
        text-align: center;
      }
      .elementor-23 .elementor-element.elementor-element-9c1a4d6 {
        transition: background 0.3s, border 0.3s, border-radius 0.3s,
          box-shadow 0.3s;
        padding: 43px 0px 15px 0px;
      }
      .elementor-23
        .elementor-element.elementor-element-9c1a4d6
        > .elementor-background-overlay {
        transition: background 0.3s, border-radius 0.3s, opacity 0.3s;
      }
      .elementor-23
        .elementor-element.elementor-element-34f96bd
        > .elementor-element-populated {
        transition: background 0.3s, border 0.3s, border-radius 0.3s,
          box-shadow 0.3s;
        margin: 5px 5px 5px 5px;
        --e-column-margin-right: 5px;
        --e-column-margin-left: 5px;
      }
      .elementor-23
        .elementor-element.elementor-element-34f96bd
        > .elementor-element-populated
        > .elementor-background-overlay {
        transition: background 0.3s, border-radius 0.3s, opacity 0.3s;
      }
      .elementor-23 .elementor-element.elementor-element-aea52c0 {
        text-align: center;
      }
      .elementor-23
        .elementor-element.elementor-element-aea52c0
        .elementor-heading-title {
        color: #f6e9e4;
        font-family: "Ephesis", Sans-serif;
        font-size: 120px;
        font-weight: 300;
      }
      .elementor-23 .elementor-element.elementor-element-ec0fe2e {
        text-align: center;
        top: 83px;
      }
      .elementor-23
        .elementor-element.elementor-element-ec0fe2e
        .elementor-heading-title {
        color: #000000;
        font-family: "cocosharp", Sans-serif;
        font-size: 48px;
        font-weight: 300;
        letter-spacing: 1.8px;
      }
      body:not(.rtl)
        .elementor-23
        .elementor-element.elementor-element-ec0fe2e {
        left: 6px;
      }
      body.rtl .elementor-23 .elementor-element.elementor-element-ec0fe2e {
        right: 6px;
      }
      .elementor-23 .elementor-element.elementor-element-796bc7e {
        --spacer-size: 50px;
      }
      :root {
        --page-title-display: none;
      }
      @media (min-width: 768px) {
        .elementor-23 .elementor-element.elementor-element-2d91d24 {
          width: 46.022%;
        }
        .elementor-23 .elementor-element.elementor-element-10eeab08 {
          width: 53.976%;
        }
        .elementor-23 .elementor-element.elementor-element-65d6aa36 {
          width: 46.091%;
        }
        .elementor-23 .elementor-element.elementor-element-75ef02b2 {
          width: 53.909%;
        }
        .elementor-23 .elementor-element.elementor-element-373fb87d {
          width: 40.278%;
        }
        .elementor-23 .elementor-element.elementor-element-62d2ac28 {
          width: 59.722%;
        }
        .elementor-23 .elementor-element.elementor-element-1c1942eb {
          width: 46.022%;
        }
        .elementor-23 .elementor-element.elementor-element-3178c001 {
          width: 53.976%;
        }
        .elementor-23 .elementor-element.elementor-element-6fd3d42a {
          width: 46.091%;
        }
        .elementor-23 .elementor-element.elementor-element-31876c4b {
          width: 53.909%;
        }
        .elementor-23 .elementor-element.elementor-element-3dde861f {
          width: 42.56%;
        }
        .elementor-23 .elementor-element.elementor-element-32bc5957 {
          width: 57.44%;
        }
      }
      @media (max-width: 1024px) {
        .elementor-23
          .elementor-element.elementor-element-a2d5ce9
          .elementor-heading-title {
          font-size: 150px;
        }
        .elementor-23 .elementor-element.elementor-element-159885d0 {
          text-align: center;
          top: 80px;
        }
        .elementor-23
          .elementor-element.elementor-element-159885d0
          .elementor-heading-title {
          font-size: 33px;
          letter-spacing: 1.1px;
          word-spacing: 0.1em;
        }
        body:not(.rtl)
          .elementor-23
          .elementor-element.elementor-element-159885d0 {
          left: -11px;
        }
        body.rtl .elementor-23 .elementor-element.elementor-element-159885d0 {
          right: -11px;
        }
        body:not(.rtl)
          .elementor-23
          .elementor-element.elementor-element-d8c839 {
          left: 0px;
        }
        body.rtl .elementor-23 .elementor-element.elementor-element-d8c839 {
          right: 0px;
        }
        .elementor-23 .elementor-element.elementor-element-d8c839 {
          top: 175px;
        }
        .elementor-23 .elementor-element.elementor-element-731cba6a {
          --spacer-size: 152px;
        }
        body:not(.rtl)
          .elementor-23
          .elementor-element.elementor-element-56b8a169 {
          left: -150px;
        }
        body.rtl .elementor-23 .elementor-element.elementor-element-56b8a169 {
          right: -150px;
        }
        .elementor-23
          .elementor-element.elementor-element-e84427b
          .elementor-heading-title {
          font-size: 92px;
        }
        .elementor-23 .elementor-element.elementor-element-7cd43d6f {
          text-align: center;
        }
        .elementor-23 .elementor-element.elementor-element-2810f674 {
          text-align: left;
        }
        .elementor-23
          .elementor-element.elementor-element-2810f674
          .elementor-heading-title {
          font-size: 120px;
        }
        .elementor-23
          .elementor-element.elementor-element-2810f674
          > .elementor-widget-container {
          margin: 0px 0px 0px 28px;
        }
        .elementor-23 .elementor-element.elementor-element-4809f91 {
          text-align: center;
          top: 70px;
        }
        .elementor-23
          .elementor-element.elementor-element-4809f91
          .elementor-heading-title {
          font-size: 35px;
          letter-spacing: 3px;
        }
        body:not(.rtl)
          .elementor-23
          .elementor-element.elementor-element-4809f91 {
          left: 0px;
        }
        body.rtl .elementor-23 .elementor-element.elementor-element-4809f91 {
          right: 0px;
        }
        .elementor-23
          .elementor-element.elementor-element-80e0e47
          > .elementor-widget-container {
          margin: 11px 0px 0px 0px;
        }
        .elementor-23
          .elementor-element.elementor-element-3195d44c
          > .elementor-widget-container {
          margin: 20px 20px 20px 20px;
        }
        .elementor-23
          .elementor-element.elementor-element-78b665e9
          > .elementor-widget-container {
          margin: 20px 20px 20px 20px;
        }
        .elementor-23 .elementor-element.elementor-element-18557835 {
          margin-top: 30px;
          margin-bottom: 30px;
        }
        .elementor-23
          .elementor-element.elementor-element-57399bab
          > .elementor-widget-container {
          margin: 20px 20px 20px 20px;
        }
        .elementor-23
          .elementor-element.elementor-element-3003f7ec
          > .elementor-widget-container {
          margin: 20px 20px 20px 20px;
        }
        .elementor-23
          .elementor-element.elementor-element-3ad85a42
          .elementor-heading-title {
          font-size: 135px;
        }
        .elementor-23
          .elementor-element.elementor-element-16bad9ca
          .elementor-heading-title {
          font-size: 30px;
        }
        body:not(.rtl)
          .elementor-23
          .elementor-element.elementor-element-84b5cc6 {
          left: 308px;
        }
        body.rtl .elementor-23 .elementor-element.elementor-element-84b5cc6 {
          right: 308px;
        }
        body:not(.rtl)
          .elementor-23
          .elementor-element.elementor-element-4f5c91df {
          left: 308px;
        }
        body.rtl .elementor-23 .elementor-element.elementor-element-4f5c91df {
          right: 308px;
        }
        .elementor-23 .elementor-element.elementor-element-2f8d00a4 {
          padding: 0px 0px 0px 0px;
        }
        .elementor-23
          .elementor-element.elementor-element-5ee330ba
          .elementor-heading-title {
          font-size: 100px;
          line-height: 1.7em;
        }
        .elementor-23
          .elementor-element.elementor-element-215608c9
          .elementor-heading-title {
          font-size: 42px;
          line-height: 2.2em;
          letter-spacing: 2.1px;
        }
        .elementor-23
          .elementor-element.elementor-element-215608c9
          > .elementor-widget-container {
          margin: 0px 0px 0px 0px;
        }
        body:not(.rtl)
          .elementor-23
          .elementor-element.elementor-element-215608c9 {
          left: 0px;
        }
        body.rtl .elementor-23 .elementor-element.elementor-element-215608c9 {
          right: 0px;
        }
        .elementor-23 .elementor-element.elementor-element-215608c9 {
          top: 79px;
        }
        .elementor-23 .elementor-element.elementor-element-180780ca {
          padding: 0px 0px 0px 0px;
        }
        .elementor-23
          .elementor-element.elementor-element-4cee084f
          .elementor-heading-title {
          font-size: 100px;
          line-height: 1.7em;
        }
        .elementor-23
          .elementor-element.elementor-element-231ea8c8
          .elementor-heading-title {
          font-size: 42px;
          line-height: 2.2em;
          letter-spacing: 2.1px;
        }
        .elementor-23
          .elementor-element.elementor-element-231ea8c8
          > .elementor-widget-container {
          margin: 0px 0px 0px 0px;
        }
        body:not(.rtl)
          .elementor-23
          .elementor-element.elementor-element-231ea8c8 {
          left: 0px;
        }
        body.rtl .elementor-23 .elementor-element.elementor-element-231ea8c8 {
          right: 0px;
        }
        .elementor-23 .elementor-element.elementor-element-231ea8c8 {
          top: 79px;
        }
        .elementor-23 .elementor-element.elementor-element-3feecfe {
          padding: 0px 0px 0px 0px;
        }
        .elementor-23
          .elementor-element.elementor-element-85eca84
          .elementor-heading-title {
          font-size: 100px;
          line-height: 1.7em;
        }
        .elementor-23
          .elementor-element.elementor-element-9093ef1
          .elementor-heading-title {
          font-size: 42px;
          line-height: 2.2em;
          letter-spacing: 2.1px;
        }
        .elementor-23
          .elementor-element.elementor-element-9093ef1
          > .elementor-widget-container {
          margin: 0px 0px 0px 0px;
        }
        body:not(.rtl)
          .elementor-23
          .elementor-element.elementor-element-9093ef1 {
          left: 0px;
        }
        body.rtl .elementor-23 .elementor-element.elementor-element-9093ef1 {
          right: 0px;
        }
        .elementor-23 .elementor-element.elementor-element-9093ef1 {
          top: 79px;
        }
        .elementor-23 .elementor-element.elementor-element-a1c33cc {
          padding: 0px 0px 0px 0px;
        }
        .elementor-23
          .elementor-element.elementor-element-f5dfa08
          .elementor-heading-title {
          font-size: 100px;
          line-height: 1.7em;
        }
        .elementor-23
          .elementor-element.elementor-element-f94cdc3
          .elementor-heading-title {
          font-size: 42px;
          line-height: 2.2em;
          letter-spacing: 2.1px;
        }
        .elementor-23
          .elementor-element.elementor-element-f94cdc3
          > .elementor-widget-container {
          margin: 0px 0px 0px 0px;
        }
        body:not(.rtl)
          .elementor-23
          .elementor-element.elementor-element-f94cdc3 {
          left: 0px;
        }
        body.rtl .elementor-23 .elementor-element.elementor-element-f94cdc3 {
          right: 0px;
        }
        .elementor-23 .elementor-element.elementor-element-f94cdc3 {
          top: 79px;
        }
        .elementor-23
          .elementor-element.elementor-element-dce8588
          .elementor-heading-title {
          font-size: 132px;
        }
        .elementor-23
          .elementor-element.elementor-element-3b10034e
          .elementor-heading-title {
          font-size: 29px;
        }
        .elementor-23
          .elementor-element.elementor-element-17a23b7d
          .elementor-heading-title {
          font-size: 123px;
        }
        .elementor-23
          .elementor-element.elementor-element-5e23f225
          .elementor-heading-title {
          font-size: 24px;
        }
        .elementor-23
          .elementor-element.elementor-element-49871dee
          .elementor-heading-title {
          font-size: 90px;
          line-height: 1.6em;
        }
        .elementor-23
          .elementor-element.elementor-element-79cd7484
          .elementor-heading-title {
          font-size: 41px;
        }
        .elementor-23
          .elementor-element.elementor-element-1f2e84af
          .elementor-heading-title {
          font-size: 90px;
        }
        .elementor-23
          .elementor-element.elementor-element-7d8c2138
          .elementor-heading-title {
          font-size: 41px;
        }
        body:not(.rtl)
          .elementor-23
          .elementor-element.elementor-element-7d8c2138 {
          left: -34px;
        }
        body.rtl .elementor-23 .elementor-element.elementor-element-7d8c2138 {
          right: -34px;
        }
        .elementor-23 .elementor-element.elementor-element-7d8c2138 {
          top: 251px;
        }
        .elementor-23 .elementor-element.elementor-element-9c1a4d6 {
          padding: 0px 0px 0px 0px;
        }
        .elementor-23
          .elementor-element.elementor-element-aea52c0
          .elementor-heading-title {
          font-size: 100px;
          line-height: 1.7em;
        }
        .elementor-23
          .elementor-element.elementor-element-ec0fe2e
          .elementor-heading-title {
          font-size: 42px;
          line-height: 2.2em;
          letter-spacing: 2.1px;
        }
        .elementor-23
          .elementor-element.elementor-element-ec0fe2e
          > .elementor-widget-container {
          margin: 0px 0px 0px 0px;
        }
        body:not(.rtl)
          .elementor-23
          .elementor-element.elementor-element-ec0fe2e {
          left: 0px;
        }
        body.rtl .elementor-23 .elementor-element.elementor-element-ec0fe2e {
          right: 0px;
        }
        .elementor-23 .elementor-element.elementor-element-ec0fe2e {
          top: 79px;
        }
      }
      @media (max-width: 767px) {
        .elementor-23 .elementor-element.elementor-element-d4a51af {
          margin-top: 20px;
          margin-bottom: 20px;
          padding: 10px 10px 10px 10px;
        }
        .elementor-23 .elementor-element.elementor-element-a2d5ce9 {
          text-align: center;
        }
        .elementor-23
          .elementor-element.elementor-element-a2d5ce9
          .elementor-heading-title {
          font-size: 146px;
        }
        .elementor-23 .elementor-element.elementor-element-159885d0 {
          text-align: center;
          top: 96px;
        }
        .elementor-23
          .elementor-element.elementor-element-159885d0
          .elementor-heading-title {
          font-size: 33px;
        }
        .elementor-23
          .elementor-element.elementor-element-159885d0
          > .elementor-widget-container {
          margin: 0px 0px 0px 0px;
        }
        body:not(.rtl)
          .elementor-23
          .elementor-element.elementor-element-159885d0 {
          left: 2px;
        }
        body.rtl .elementor-23 .elementor-element.elementor-element-159885d0 {
          right: 2px;
        }
        .elementor-23
          .elementor-element.elementor-element-d8c839
          .elementor-divider {
          text-align: center;
        }
        .elementor-23
          .elementor-element.elementor-element-d8c839
          .elementor-divider-separator {
          margin: 0 auto;
          margin-center: 0;
        }
        .elementor-23
          .elementor-element.elementor-element-d8c839
          > .elementor-widget-container {
          margin: 7px 0px 0px 0px;
        }
        body:not(.rtl)
          .elementor-23
          .elementor-element.elementor-element-d8c839 {
          left: 1px;
        }
        body.rtl .elementor-23 .elementor-element.elementor-element-d8c839 {
          right: 1px;
        }
        .elementor-23 .elementor-element.elementor-element-d8c839 {
          top: 150px;
        }
        .elementor-23 .elementor-element.elementor-element-720da442 {
          text-align: center;
        }
        .elementor-23 .elementor-element.elementor-element-6dad0e2a {
          --spacer-size: 32px;
        }
        .elementor-23 .elementor-element.elementor-element-731cba6a {
          --spacer-size: 400px;
        }
        body:not(.rtl)
          .elementor-23
          .elementor-element.elementor-element-56b8a169 {
          left: -103px;
        }
        body.rtl .elementor-23 .elementor-element.elementor-element-56b8a169 {
          right: -103px;
        }
        .elementor-23 .elementor-element.elementor-element-56b8a169 {
          bottom: -92px;
        }
        .elementor-23 .elementor-element.elementor-element-e84427b {
          text-align: center;
        }
        .elementor-23
          .elementor-element.elementor-element-e84427b
          .elementor-heading-title {
          font-size: 60px;
        }
        .elementor-23
          .elementor-element.elementor-element-e84427b
          > .elementor-widget-container {
          margin: 10px 10px 10px 10px;
        }
        .elementor-23
          .elementor-element.elementor-element-2de21ff
          .elementor-heading-title {
          font-size: 22px;
        }
        .elementor-23 .elementor-element.elementor-element-573b176 .cat-name {
          font-size: 12px;
        }
        .elementor-23 .elementor-element.elementor-element-2810f674 {
          text-align: center;
        }
        .elementor-23
          .elementor-element.elementor-element-2810f674
          .elementor-heading-title {
          font-size: 116px;
        }
        .elementor-23
          .elementor-element.elementor-element-4809f91
          .elementor-heading-title {
          font-size: 30px;
        }
        .elementor-23
          .elementor-element.elementor-element-4809f91
          > .elementor-widget-container {
          margin: 0px 0px 0px 0px;
        }
        body:not(.rtl)
          .elementor-23
          .elementor-element.elementor-element-4809f91 {
          left: 11px;
        }
        body.rtl .elementor-23 .elementor-element.elementor-element-4809f91 {
          right: 11px;
        }
        .elementor-23 .elementor-element.elementor-element-4809f91 {
          top: 67px;
        }
        .elementor-23
          .elementor-element.elementor-element-3195d44c
          .elementor-icon {
          font-size: 40px;
        }
        .elementor-23
          .elementor-element.elementor-element-78b665e9
          .elementor-icon {
          font-size: 40px;
        }
        .elementor-23
          .elementor-element.elementor-element-57399bab
          .elementor-icon {
          font-size: 40px;
        }
        .elementor-23
          .elementor-element.elementor-element-3003f7ec
          .elementor-icon {
          font-size: 40px;
        }
        .elementor-23 .elementor-element.elementor-element-26b15076 {
          --spacer-size: 339px;
        }
        .elementor-23
          .elementor-element.elementor-element-3ad85a42
          .elementor-heading-title {
          font-size: 95px;
        }
        .elementor-23
          .elementor-element.elementor-element-16bad9ca
          .elementor-heading-title {
          font-size: 24px;
        }
        .elementor-23 .elementor-element.elementor-element-16bad9ca {
          top: 53px;
        }
        .elementor-23 .elementor-element.elementor-element-7adcf66 {
          padding: 0px 0px 0px 0px;
        }
        .elementor-23
          .elementor-element.elementor-element-31c9c1fa
          .elementor-icon {
          font-size: 40px;
        }
        .elementor-23
          .elementor-element.elementor-element-31c9c1fa
          > .elementor-widget-container {
          margin: 20px 20px 20px 20px;
        }
        .elementor-23
          .elementor-element.elementor-element-2c214dca
          .elementor-icon {
          font-size: 40px;
        }
        .elementor-23
          .elementor-element.elementor-element-2c214dca
          > .elementor-widget-container {
          margin: 20px 20px 20px 20px;
        }
        .elementor-23
          .elementor-element.elementor-element-5daae136
          .elementor-icon {
          font-size: 40px;
        }
        .elementor-23
          .elementor-element.elementor-element-5daae136
          > .elementor-widget-container {
          margin: 20px 20px 20px 20px;
        }
        .elementor-23
          .elementor-element.elementor-element-130c3e4b
          .elementor-icon {
          font-size: 40px;
        }
        .elementor-23
          .elementor-element.elementor-element-130c3e4b
          > .elementor-widget-container {
          margin: 20px 20px 20px 20px;
        }
        body:not(.rtl)
          .elementor-23
          .elementor-element.elementor-element-84b5cc6 {
          left: 147px;
        }
        body.rtl .elementor-23 .elementor-element.elementor-element-84b5cc6 {
          right: 147px;
        }
        body:not(.rtl)
          .elementor-23
          .elementor-element.elementor-element-4f5c91df {
          left: 147px;
        }
        body.rtl .elementor-23 .elementor-element.elementor-element-4f5c91df {
          right: 147px;
        }
        .elementor-23 .elementor-element.elementor-element-2f8d00a4 {
          padding: 12px 0px 13px 0px;
        }
        .elementor-23
          .elementor-element.elementor-element-5ee330ba
          .elementor-heading-title {
          font-size: 74px;
          line-height: 1.1em;
          letter-spacing: -1px;
        }
        .elementor-23 .elementor-element.elementor-element-215608c9 {
          text-align: center;
          top: 60px;
        }
        .elementor-23
          .elementor-element.elementor-element-215608c9
          .elementor-heading-title {
          font-size: 28px;
          line-height: 0.8em;
        }
        .elementor-23
          .elementor-element.elementor-element-215608c9
          > .elementor-widget-container {
          margin: -2px 0px 0px 0px;
        }
        body:not(.rtl)
          .elementor-23
          .elementor-element.elementor-element-215608c9 {
          left: 3px;
        }
        body.rtl .elementor-23 .elementor-element.elementor-element-215608c9 {
          right: 3px;
        }
        .elementor-23 .elementor-element.elementor-element-180780ca {
          padding: 12px 0px 13px 0px;
        }
        .elementor-23
          .elementor-element.elementor-element-4cee084f
          .elementor-heading-title {
          font-size: 74px;
          line-height: 1.1em;
          letter-spacing: -1px;
        }
        .elementor-23 .elementor-element.elementor-element-231ea8c8 {
          text-align: center;
          top: 60px;
        }
        .elementor-23
          .elementor-element.elementor-element-231ea8c8
          .elementor-heading-title {
          font-size: 32px;
          line-height: 0.8em;
        }
        .elementor-23
          .elementor-element.elementor-element-231ea8c8
          > .elementor-widget-container {
          margin: -2px 0px 0px 0px;
        }
        body:not(.rtl)
          .elementor-23
          .elementor-element.elementor-element-231ea8c8 {
          left: 3px;
        }
        body.rtl .elementor-23 .elementor-element.elementor-element-231ea8c8 {
          right: 3px;
        }
        .elementor-23 .elementor-element.elementor-element-3feecfe {
          padding: 12px 0px 13px 0px;
        }
        .elementor-23
          .elementor-element.elementor-element-85eca84
          .elementor-heading-title {
          font-size: 74px;
          line-height: 1.1em;
          letter-spacing: -1px;
        }
        .elementor-23 .elementor-element.elementor-element-9093ef1 {
          text-align: center;
          top: 60px;
        }
        .elementor-23
          .elementor-element.elementor-element-9093ef1
          .elementor-heading-title {
          font-size: 32px;
          line-height: 0.8em;
        }
        .elementor-23
          .elementor-element.elementor-element-9093ef1
          > .elementor-widget-container {
          margin: -2px 0px 0px 0px;
        }
        body:not(.rtl)
          .elementor-23
          .elementor-element.elementor-element-9093ef1 {
          left: 3px;
        }
        body.rtl .elementor-23 .elementor-element.elementor-element-9093ef1 {
          right: 3px;
        }
        .elementor-23 .elementor-element.elementor-element-a1c33cc {
          padding: 12px 0px 13px 0px;
        }
        .elementor-23
          .elementor-element.elementor-element-f5dfa08
          .elementor-heading-title {
          font-size: 74px;
          line-height: 1.1em;
          letter-spacing: -1px;
        }
        .elementor-23 .elementor-element.elementor-element-f94cdc3 {
          text-align: center;
          top: 60px;
        }
        .elementor-23
          .elementor-element.elementor-element-f94cdc3
          .elementor-heading-title {
          font-size: 32px;
          line-height: 0.8em;
        }
        .elementor-23
          .elementor-element.elementor-element-f94cdc3
          > .elementor-widget-container {
          margin: -2px 0px 0px 0px;
        }
        body:not(.rtl)
          .elementor-23
          .elementor-element.elementor-element-f94cdc3 {
          left: 3px;
        }
        body.rtl .elementor-23 .elementor-element.elementor-element-f94cdc3 {
          right: 3px;
        }
        .elementor-23 .elementor-element.elementor-element-dce8588 {
          text-align: center;
        }
        .elementor-23
          .elementor-element.elementor-element-dce8588
          .elementor-heading-title {
          font-size: 146px;
        }
        .elementor-23 .elementor-element.elementor-element-3b10034e {
          text-align: center;
        }
        .elementor-23
          .elementor-element.elementor-element-3b10034e
          .elementor-heading-title {
          font-size: 22px;
        }
        .elementor-23
          .elementor-element.elementor-element-64a63cf:not(.elementor-motion-effects-element-type-background)
          > .elementor-widget-wrap,
        .elementor-23
          .elementor-element.elementor-element-64a63cf
          > .elementor-widget-wrap
          > .elementor-motion-effects-container
          > .elementor-motion-effects-layer {
          background-position: top center;
          background-size: cover;
        }
        .elementor-23 .elementor-element.elementor-element-17a23b7d {
          text-align: center;
        }
        .elementor-23
          .elementor-element.elementor-element-17a23b7d
          .elementor-heading-title {
          font-size: 146px;
        }
        .elementor-23 .elementor-element.elementor-element-5e23f225 {
          text-align: center;
        }
        .elementor-23 .elementor-element.elementor-element-5044eaa5 {
          text-align: center;
        }
        .elementor-23
          .elementor-element.elementor-element-32bc5957
          > .elementor-element-populated {
          margin: 0px 0px 0px 0px;
          --e-column-margin-right: 0px;
          --e-column-margin-left: 0px;
        }
        .elementor-23
          .elementor-element.elementor-element-49871dee
          .elementor-heading-title {
          font-size: 83px;
        }
        .elementor-23
          .elementor-element.elementor-element-49871dee
          > .elementor-widget-container {
          margin: 0px 0px 0px 0px;
        }
        .elementor-23 .elementor-element.elementor-element-79cd7484 {
          text-align: center;
        }
        .elementor-23
          .elementor-element.elementor-element-79cd7484
          .elementor-heading-title {
          font-size: 37px;
        }
        .elementor-23
          .elementor-element.elementor-element-79cd7484
          > .elementor-widget-container {
          margin: 0px 0px 0px 0px;
        }
        .elementor-23
          .elementor-element.elementor-element-5e232fd4
          .elementor-divider-separator {
          width: 33%;
        }
        .elementor-23 .elementor-element.elementor-element-1f2e84af {
          text-align: center;
        }
        .elementor-23
          .elementor-element.elementor-element-1f2e84af
          .elementor-heading-title {
          font-size: 83px;
        }
        .elementor-23
          .elementor-element.elementor-element-1f2e84af
          > .elementor-widget-container {
          margin: 0px 0px 0px 0px;
        }
        .elementor-23 .elementor-element.elementor-element-7d8c2138 {
          text-align: center;
          top: 50px;
        }
        .elementor-23
          .elementor-element.elementor-element-7d8c2138
          .elementor-heading-title {
          font-size: 25px;
        }
        .elementor-23
          .elementor-element.elementor-element-7d8c2138
          > .elementor-widget-container {
          margin: 0px 0px 0px 0px;
        }
        body:not(.rtl)
          .elementor-23
          .elementor-element.elementor-element-7d8c2138 {
          left: -1px;
        }
        body.rtl .elementor-23 .elementor-element.elementor-element-7d8c2138 {
          right: -1px;
        }
        .elementor-23
          .elementor-element.elementor-element-78ec5763
          .elementor-divider-separator {
          width: 20%;
        }
        .elementor-23 .elementor-element.elementor-element-9c1a4d6 {
          padding: 12px 0px 13px 0px;
        }
        .elementor-23
          .elementor-element.elementor-element-aea52c0
          .elementor-heading-title {
          font-size: 74px;
          line-height: 1.1em;
          letter-spacing: -1px;
        }
        .elementor-23 .elementor-element.elementor-element-ec0fe2e {
          text-align: center;
          top: 60px;
        }
        .elementor-23
          .elementor-element.elementor-element-ec0fe2e
          .elementor-heading-title {
          font-size: 32px;
          line-height: 0.8em;
        }
        .elementor-23
          .elementor-element.elementor-element-ec0fe2e
          > .elementor-widget-container {
          margin: -2px 0px 0px 0px;
        }
        body:not(.rtl)
          .elementor-23
          .elementor-element.elementor-element-ec0fe2e {
          left: 3px;
        }
        body.rtl .elementor-23 .elementor-element.elementor-element-ec0fe2e {
          right: 3px;
        }
      }
      @media (min-width: 1025px) {
        .elementor-23
          .elementor-element.elementor-element-7cce41b1:not(.elementor-motion-effects-element-type-background)
          > .elementor-widget-wrap,
        .elementor-23
          .elementor-element.elementor-element-7cce41b1
          > .elementor-widget-wrap
          > .elementor-motion-effects-container
          > .elementor-motion-effects-layer {
          background-attachment: scroll;
        }
        .elementor-23
          .elementor-element.elementor-element-137a3f18:not(.elementor-motion-effects-element-type-background)
          > .elementor-widget-wrap,
        .elementor-23
          .elementor-element.elementor-element-137a3f18
          > .elementor-widget-wrap
          > .elementor-motion-effects-container
          > .elementor-motion-effects-layer {
          background-attachment: scroll;
        }
        .elementor-23
          .elementor-element.elementor-element-64a63cf:not(.elementor-motion-effects-element-type-background)
          > .elementor-widget-wrap,
        .elementor-23
          .elementor-element.elementor-element-64a63cf
          > .elementor-widget-wrap
          > .elementor-motion-effects-container
          > .elementor-motion-effects-layer {
          background-attachment: fixed;
        }
        .elementor-23
          .elementor-element.elementor-element-3dde861f:not(.elementor-motion-effects-element-type-background)
          > .elementor-widget-wrap,
        .elementor-23
          .elementor-element.elementor-element-3dde861f
          > .elementor-widget-wrap
          > .elementor-motion-effects-container
          > .elementor-motion-effects-layer {
          background-attachment: scroll;
        }
      }
      .elementor-51
        .elementor-element.elementor-element-c92871a:not(.elementor-motion-effects-element-type-background),
      .elementor-51
        .elementor-element.elementor-element-c92871a
        > .elementor-motion-effects-container
        > .elementor-motion-effects-layer {
        background-color: #1d1c1c;
      }
      .elementor-51 .elementor-element.elementor-element-c92871a {
        transition: background 0.3s, border 0.3s, border-radius 0.3s,
          box-shadow 0.3s;
        margin-top: 0px;
        margin-bottom: 0px;
        padding: 6px 6px 6px 6px;
      }
      .elementor-51
        .elementor-element.elementor-element-c92871a
        > .elementor-background-overlay {
        transition: background 0.3s, border-radius 0.3s, opacity 0.3s;
      }
      .elementor-51 .elementor-element.elementor-element-8995130 {
        text-align: center;
      }
      .elementor-51
        .elementor-element.elementor-element-8995130
        .elementor-heading-title {
        color: #ffffff;
        font-size: 16px;
      }
      .elementor-51
        .elementor-element.elementor-element-73fc734
        > .elementor-container
        > .elementor-column
        > .elementor-widget-wrap {
        align-content: center;
        align-items: center;
      }
      .elementor-51
        .elementor-element.elementor-element-73fc734:not(.elementor-motion-effects-element-type-background),
      .elementor-51
        .elementor-element.elementor-element-73fc734
        > .elementor-motion-effects-container
        > .elementor-motion-effects-layer {
        background-color: #2c2c2c;
      }
      .elementor-51 .elementor-element.elementor-element-73fc734 {
        border-style: solid;
        border-width: 0px 0px 1px 0px;
        border-color: #ffffff2e;
        transition: background 0.3s, border 0.3s, border-radius 0.3s,
          box-shadow 0.3s;
        padding: 16px 0px 15px 0px;
      }
      .elementor-51
        .elementor-element.elementor-element-73fc734
        > .elementor-background-overlay {
        transition: background 0.3s, border-radius 0.3s, opacity 0.3s;
      }
      .elementor-51 .elementor-element.elementor-element-9d6c1d4 img {
        max-width: 148px;
      }
      .elementor-51
        .elementor-element.elementor-element-43cd067
        .elementor-nav-menu {
        justify-content: center !important;
      }
      .elementor-51
        .elementor-element.elementor-element-43cd067
        .tbay-horizontal
        .navbar-nav
        > li
        > a:after {
        display: none !important;
      }
      .elementor-51
        .elementor-element.elementor-element-43cd067
        .elementor-nav-menu--main.tbay-horizontal
        > ul
        > li
        > a {
        color: #ffffff;
      }
      .elementor-51
        .elementor-element.elementor-element-43cd067
        .elementor-nav-menu--main.tbay-horizontal
        > ul
        > li
        > a
        i {
        color: #ffffff;
      }
      .elementor-51
        .elementor-element.elementor-element-43cd067
        .elementor-nav-menu--main.tbay-horizontal
        > ul
        > li
        > .caret:before {
        background-color: #ffffff;
      }
      .elementor-51
        .elementor-element.elementor-element-43cd067
        .elementor-nav-menu--main.tbay-horizontal
        > ul
        > li
        > a:hover,
      .elementor-51
        .elementor-element.elementor-element-43cd067
        .tbay-element-nav-menu
        .elementor-nav-menu--main.tbay-horizontal
        > ul
        > li:hover
        > a
        > .caret,
      .elementor-51
        .elementor-element.elementor-element-43cd067
        .tbay-element-nav-menu
        .elementor-nav-menu--main.tbay-horizontal
        > ul
        > li:focus
        > a
        > .caret,
      .elementor-51
        .elementor-element.elementor-element-43cd067
        .tbay-element-nav-menu
        .elementor-nav-menu--main.tbay-horizontal
        > ul
        > li.current-menu-parent
        > a
        > .caret,
      .elementor-51
        .elementor-element.elementor-element-43cd067
        .elementor-nav-menu--main.tbay-horizontal
        > ul
        > li
        > a:hover
        i,
      .elementor-51
        .elementor-element.elementor-element-43cd067
        .elementor-nav-menu--main.tbay-horizontal
        > ul
        > li
        > a:focus
        i,
      .elementor-51
        .elementor-element.elementor-element-43cd067
        .elementor-nav-menu--main.tbay-horizontal
        > ul
        > li
        > a.active
        i,
      .elementor-51
        .elementor-element.elementor-element-43cd067
        .elementor-nav-menu--main.tbay-horizontal
        > ul
        > li
        > a:focus,
      .elementor-51
        .elementor-element.elementor-element-43cd067
        .elementor-nav-menu--main.tbay-horizontal
        > ul
        > li.current-menu-parent
        > a,
      .elementor-51
        .elementor-element.elementor-element-43cd067
        .tbay-element-nav-menu
        .elementor-nav-menu--main.tbay-horizontal
        > ul
        > li.current-menu-item
        > a,
      .elementor-51
        .elementor-element.elementor-element-43cd067
        .elementor-nav-menu--main.tbay-horizontal
        > ul
        > li.current_page_item
        > a,
      .elementor-51
        .elementor-element.elementor-element-43cd067
        .elementor-nav-menu--main.tbay-horizontal
        > ul
        > li.current_page_parent
        > a {
        color: #ffffff !important;
      }
      .elementor-51
        .elementor-element.elementor-element-43cd067
        .elementor-nav-menu--main
        .elementor-item {
        padding: 26px 0px 26px 0px;
      }
      .elementor-51
        .elementor-element.elementor-element-43cd067
        .elementor-nav-menu--main
        .dropdown-menu
        .elementor-item {
        padding: 0;
      }
      .elementor-51 .elementor-element.elementor-element-43cd067 {
        width: var(--container-widget-width, 106.424%);
        max-width: 106.424%;
        --container-widget-width: 106.424%;
        --container-widget-flex-grow: 0;
      }
      .elementor-51
        .elementor-element.elementor-element-e6161df
        > div.elementor-element-populated {
        padding: 0px 0px 0px 18px !important;
      }
      .elementor-51
        .elementor-element.elementor-element-b54d16e.elementor-column
        > .elementor-widget-wrap {
        justify-content: flex-end;
      }
      .elementor-51
        .elementor-element.elementor-element-b54d16e
        > div.elementor-element-populated {
        padding: 0px 18px 0px 0px !important;
      }
      .elementor-51
        .elementor-element.elementor-element-74a6cf1
        .cart-dropdown
        .cart-icon {
        color: #ffffff;
        background-color: #02010100;
      }
      .elementor-51
        .elementor-element.elementor-element-74a6cf1
        .cart-dropdown
        .cart-icon:hover {
        color: #ffffff;
      }
      .elementor-51
        .elementor-element.elementor-element-74a6cf1
        .cart-icon
        span.mini-cart-items {
        font-size: 12px;
        font-weight: 400;
      }
      .elementor-51
        .elementor-element.elementor-element-74a6cf1
        .cart-popup
        .dropdown-menu.show {
        inset: 51px 0px auto auto !important;
      }
      .rtl
        .elementor-51
        .elementor-element.elementor-element-74a6cf1
        .cart-popup
        .dropdown-menu.show {
        inset: 51px auto auto 0px !important;
      }
      .elementor-51
        .elementor-element.elementor-element-41757a2
        .tbay-login
        a
        i {
        font-size: 20px !important;
        color: #ffffff;
      }
      .elementor-51
        .elementor-element.elementor-element-41757a2
        .tbay-login
        > a:hover
        i {
        color: #ffffff;
      }
      .elementor-51
        .elementor-element.elementor-element-baae983
        .top-wishlist
        i {
        color: #ffffff;
      }
      .elementor-51
        .elementor-element.elementor-element-baae983
        .top-wishlist
        a:hover
        i {
        color: #ffffff;
      }
      .elementor-51
        .elementor-element.elementor-element-baae983
        .top-wishlist
        .count_wishlist {
        font-size: 12px;
        font-weight: 400;
      }
      @media (min-width: 768px) {
        .elementor-51 .elementor-element.elementor-element-b88b4aa {
          width: 19.912%;
        }
        .elementor-51 .elementor-element.elementor-element-a61c64d {
          width: 57.749%;
        }
        .elementor-51 .elementor-element.elementor-element-e6161df {
          width: 5.149%;
        }
        .elementor-51 .elementor-element.elementor-element-b54d16e {
          width: 17.156%;
        }
      }
      .elementor-939
        .elementor-element.elementor-element-63862f22:not(.elementor-motion-effects-element-type-background),
      .elementor-939
        .elementor-element.elementor-element-63862f22
        > .elementor-motion-effects-container
        > .elementor-motion-effects-layer {
        background-image: url("https://zibabeauty.in/wp-content/uploads/2023/04/overhead-view-beauty-products-professional-make-up-concrete-background-scaled-1.jpg");
        background-position: center center;
        background-repeat: no-repeat;
        background-size: cover;
      }
      .elementor-939
        .elementor-element.elementor-element-63862f22
        > .elementor-background-overlay {
        background-color: #000000;
        opacity: 0.84;
        transition: background 0.3s, border-radius 0.3s, opacity 0.3s;
      }
      .elementor-939 .elementor-element.elementor-element-63862f22 {
        transition: background 0.3s, border 0.3s, border-radius 0.3s,
          box-shadow 0.3s;
      }
      .elementor-939
        .elementor-element.elementor-element-483f4d2c
        > .elementor-element-populated {
        margin: 30px 0px 0px 22px;
        --e-column-margin-right: 0px;
        --e-column-margin-left: 22px;
      }
      .elementor-939 .elementor-element.elementor-element-51f49c3a img {
        width: 38%;
      }
      .elementor-939 .elementor-element.elementor-element-d4664c1 {
        --spacer-size: 30px;
      }
      .elementor-939 .elementor-element.elementor-element-3f100d1d {
        --grid-template-columns: repeat(4, auto);
        --icon-size: 25px;
        --grid-column-gap: 8px;
        --grid-row-gap: 0px;
      }
      .elementor-939
        .elementor-element.elementor-element-3f100d1d
        .elementor-widget-container {
        text-align: center;
      }
      .elementor-939
        .elementor-element.elementor-element-3f100d1d
        .elementor-social-icon {
        background-color: #02010100;
        --icon-padding: 0.3em;
      }
      .elementor-939
        .elementor-element.elementor-element-3f100d1d
        .elementor-social-icon
        i {
        color: #ffffff;
      }
      .elementor-939
        .elementor-element.elementor-element-3f100d1d
        .elementor-social-icon
        svg {
        fill: #ffffff;
      }
      .elementor-939
        .elementor-element.elementor-element-3f100d1d
        .elementor-social-icon:hover
        i {
        color: #b0aaaa;
      }
      .elementor-939
        .elementor-element.elementor-element-3f100d1d
        .elementor-social-icon:hover
        svg {
        fill: #b0aaaa;
      }
      .elementor-939
        .elementor-element.elementor-element-3f100d1d
        > .elementor-widget-container {
        padding: 0px 0px 0px 0px;
        border-style: solid;
        border-width: 0px 0px 0px 0px;
        border-color: #919eab3d;
      }
      .elementor-939 .elementor-element.elementor-element-4a94fdf {
        --spacer-size: 30px;
      }
      .elementor-939 .elementor-element.elementor-element-230a799 {
        text-align: center;
        color: #ffffff;
      }
      .elementor-939 .elementor-element.elementor-element-51bbe304 {
        text-align: center;
      }
      .elementor-939
        .elementor-element.elementor-element-51bbe304
        .elementor-heading-title {
        color: #ffffff;
      }
      .elementor-939
        .elementor-element.elementor-element-51bbe304
        > .elementor-widget-container {
        margin: 5px 0px 0px 0px;
        padding: 10px 0px 0px 0px;
      }
      .elementor-939 .elementor-element.elementor-element-32bc31dc {
        --spacer-size: 50px;
      }
      @media (max-width: 1024px) {
        .elementor-939
          .elementor-element.elementor-element-32a9ad65
          > .elementor-widget-container {
          margin: 0px 0px 0px 0px;
        }
        .elementor-939 .elementor-element.elementor-element-32a9ad65 {
          width: 100%;
          max-width: 100%;
        }
        .elementor-939
          .elementor-element.elementor-element-3f100d1d
          > .elementor-widget-container {
          border-width: 0px 0px 0px 0px;
        }
      }
      @media (max-width: 767px) {
        .elementor-939
          .elementor-element.elementor-element-63862f22:not(.elementor-motion-effects-element-type-background),
        .elementor-939
          .elementor-element.elementor-element-63862f22
          > .elementor-motion-effects-container
          > .elementor-motion-effects-layer {
          background-size: cover;
        }
        .elementor-939 .elementor-element.elementor-element-51f49c3a {
          text-align: center;
        }
        .elementor-939
          .elementor-element.elementor-element-3f100d1d
          .elementor-widget-container {
          text-align: center;
        }
        .elementor-939
          .elementor-element.elementor-element-3f100d1d
          > .elementor-widget-container {
          margin: 0px 0px 32px 0px;
          padding: 26px 0px 32px 0px;
          border-width: 0px 0px 1px 0px;
        }
        .elementor-939
          .elementor-element.elementor-element-230a799
          > .elementor-widget-container {
          margin: 22px 0px 0px 0px;
        }
      }
      @media (min-width: 768px) {
        .elementor-939 .elementor-element.elementor-element-483f4d2c {
          width: 30.767%;
        }
        .elementor-939 .elementor-element.elementor-element-739b6f5a {
          width: 38.247%;
        }
        .elementor-939 .elementor-element.elementor-element-158fcf35 {
          width: 30.652%;
        }
      }
      @media (min-width: 1025px) {
        .elementor-939
          .elementor-element.elementor-element-63862f22:not(.elementor-motion-effects-element-type-background),
        .elementor-939
          .elementor-element.elementor-element-63862f22
          > .elementor-motion-effects-container
          > .elementor-motion-effects-layer {
          background-attachment: scroll;
        }
      }
    </style>
    <link
      data-minify="1"
      rel="stylesheet"
      id="swiper-css"
      href="https://zibabeauty.in/wp-content/cache/min/1/wp-content/plugins/elementor/assets/lib/swiper/v8/css/swiper.min.css?ver=1717589898"
      type="text/css"
      media="all"
    />
    <link
      data-minify="1"
      rel="stylesheet"
      id="font-awesome-5-all-css"
      href="https://zibabeauty.in/wp-content/cache/min/1/wp-content/plugins/elementor/assets/lib/font-awesome/css/all.min.css?ver=1717589898"
      type="text/css"
      media="all"
    />
    <link
      rel="stylesheet"
      id="font-awesome-4-shim-css"
      href="https://zibabeauty.in/wp-content/plugins/elementor/assets/lib/font-awesome/css/v4-shims.min.css?ver=3.14.1"
      type="text/css"
      media="all"
    />
    <link
      data-minify="1"
      rel="stylesheet"
      id="bootstrap-css"
      href="https://zibabeauty.in/wp-content/cache/min/1/wp-content/themes/hara/css/bootstrap.css?ver=1717589898"
      type="text/css"
      media="all"
    />
    <link
      data-minify="1"
      rel="stylesheet"
      id="hara-template-css"
      href="https://zibabeauty.in/wp-content/cache/min/1/wp-content/themes/hara/css/template.css?ver=1717589898"
      type="text/css"
      media="all"
    />
    <link
      data-minify="1"
      rel="stylesheet"
      id="hara-style-css"
      href="https://zibabeauty.in/wp-content/cache/min/1/wp-content/themes/hara/style.css?ver=1717589898"
      type="text/css"
      media="all"
    />
    <style id="hara-style-inline-css" type="text/css">
      :root {
        --tb-theme-color: #2c2c2c;
        --tb-theme-color-hover: #292929;
        --tb-header-mobile-bg: #2c2c2c;
        --tb-back-to-top-bg: #fff;
        --tb-back-to-top-bg-hover: #c4743f;
        --tb-back-to-top-color: #ffffff;
        --tb-back-to-top-color-hover: #fff;
        --tb-header-mobile-color: #ffffff;
      }
      :root {
        --tb-text-primary-font: Inter, sans-serif;
        --tb-text-second-font: Cormorant Garamond, sans-serif;
      } /* Theme Options Styles */
      .checkout-logo img {
        max-width: 120px;
      }
      @media (max-width: 1199px) {
        /* Limit logo image height for mobile according to mobile header height */
        .mobile-logo a img {
          width: 100px;
        }
        .mobile-logo a img {
          padding-top: 5px;
        }
      }
      @media screen and (max-width: 782px) {
        html body.admin-bar {
          top: -46px !important;
          position: relative;
        }
      } /* Custom CSS */
      @media screen and (max-width: 767px) {
        body #message-purchased {
          display: none !important;
        }
      }
      @media (min-width: 1200px) {
        .slick-vertical div.slick-list {
          min-height: 304px !important;
        }
      }
    </style>
    <link
      data-minify="1"
      rel="stylesheet"
      id="font-awesome-css"
      href="https://zibabeauty.in/wp-content/cache/min/1/wp-content/plugins/elementor/assets/lib/font-awesome/css/font-awesome.min.css?ver=1717589898"
      type="text/css"
      media="all"
    />
    <style id="font-awesome-inline-css" type="text/css">
      [data-font="FontAwesome"]:before {
        font-family: "FontAwesome" !important;
        content: attr(data-icon) !important;
        speak: none !important;
        font-weight: normal !important;
        font-variant: normal !important;
        text-transform: none !important;
        line-height: 1 !important;
        font-style: normal !important;
        -webkit-font-smoothing: antialiased !important;
        -moz-osx-font-smoothing: grayscale !important;
      }
    </style>
    <link
      data-minify="1"
      rel="stylesheet"
      id="hara-font-tbay-custom-css"
      href="https://zibabeauty.in/wp-content/cache/min/1/wp-content/themes/hara/css/font-tbay-custom.css?ver=1717589898"
      type="text/css"
      media="all"
    />
    <link
      data-minify="1"
      rel="stylesheet"
      id="simple-line-icons-css"
      href="https://zibabeauty.in/wp-content/cache/min/1/wp-content/themes/hara/css/simple-line-icons.css?ver=1717589898"
      type="text/css"
      media="all"
    />
    <link
      data-minify="1"
      rel="stylesheet"
      id="material-design-iconic-font-css"
      href="https://zibabeauty.in/wp-content/cache/min/1/wp-content/themes/hara/css/material-design-iconic-font.css?ver=1717589898"
      type="text/css"
      media="all"
    />
    <link
      data-minify="1"
      rel="stylesheet"
      id="animate-css"
      href="https://zibabeauty.in/wp-content/cache/min/1/wp-content/themes/hara/css/animate.css?ver=1717589898"
      type="text/css"
      media="all"
    />
    <link
      data-minify="1"
      rel="stylesheet"
      id="jquery-treeview-css"
      href="https://zibabeauty.in/wp-content/cache/min/1/wp-content/themes/hara/css/jquery.treeview.css?ver=1717589898"
      type="text/css"
      media="all"
    />
    <link
      data-minify="1"
      rel="stylesheet"
      id="magnific-popup-css"
      href="https://zibabeauty.in/wp-content/cache/min/1/wp-content/themes/hara/css/magnific-popup.css?ver=1717589898"
      type="text/css"
      media="all"
    />
    <link
      rel="stylesheet"
      id="hara-child-style-css"
      href="https://zibabeauty.in/wp-content/themes/hara-child/style.css?ver=1.1.2"
      type="text/css"
      media="all"
    />
    <link
      rel="stylesheet"
      id="google-fonts-1-css"
      href="https://fonts.googleapis.com/css?family=Roboto%3A100%2C100italic%2C200%2C200italic%2C300%2C300italic%2C400%2C400italic%2C500%2C500italic%2C600%2C600italic%2C700%2C700italic%2C800%2C800italic%2C900%2C900italic%7CRoboto+Slab%3A100%2C100italic%2C200%2C200italic%2C300%2C300italic%2C400%2C400italic%2C500%2C500italic%2C600%2C600italic%2C700%2C700italic%2C800%2C800italic%2C900%2C900italic%7CAllura%3A100%2C100italic%2C200%2C200italic%2C300%2C300italic%2C400%2C400italic%2C500%2C500italic%2C600%2C600italic%2C700%2C700italic%2C800%2C800italic%2C900%2C900italic%7CEphesis%3A100%2C100italic%2C200%2C200italic%2C300%2C300italic%2C400%2C400italic%2C500%2C500italic%2C600%2C600italic%2C700%2C700italic%2C800%2C800italic%2C900%2C900italic&#038;display=swap&#038;ver=6.6.2"
      type="text/css"
      media="all"
    />
    <link
      rel="stylesheet"
      id="elementor-icons-shared-0-css"
      href="https://zibabeauty.in/wp-content/plugins/elementor/assets/lib/font-awesome/css/fontawesome.min.css?ver=5.15.3"
      type="text/css"
      media="all"
    />
    <link
      data-minify="1"
      rel="stylesheet"
      id="elementor-icons-fa-brands-css"
      href="https://zibabeauty.in/wp-content/cache/min/1/wp-content/plugins/elementor/assets/lib/font-awesome/css/brands.min.css?ver=1717589898"
      type="text/css"
      media="all"
    />
    <link
      data-minify="1"
      rel="stylesheet"
      id="jetpack_css-css"
      href="https://zibabeauty.in/wp-content/cache/min/1/wp-content/plugins/jetpack/css/jetpack.css?ver=1717589898"
      type="text/css"
      media="all"
    />
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin />
    <script
      type="rocketlazyloadscript"
      data-rocket-type="text/javascript"
      data-rocket-src="https://zibabeauty.in/wp-includes/js/jquery/jquery.min.js?ver=3.7.1"
      id="jquery-core-js"
    ></script>
   

    <meta name="generator" content="Redux 4.4.4" />
    <style>
      img#wpstats {
        display: none;
      }
    </style>
    <noscript
      ><style>
        .woocommerce-product-gallery {
          opacity: 1 !important;
        }
      </style></noscript
    >
    <meta
      name="generator"
      content="Elementor 3.14.1; features: e_dom_optimization, e_optimized_assets_loading, e_optimized_css_loading, a11y_improvements, additional_custom_breakpoints; settings: css_print_method-internal, google_font-enabled, font_display-swap"
    />
    <meta
      name="generator"
      content="Powered by Slider Revolution 6.6.12 - responsive, Mobile-Friendly Slider Plugin for WordPress with comfortable drag and drop interface."
    />
   
    <style id="wpforms-css-vars-root">
      :root {
        --wpforms-field-border-radius: 3px;
        --wpforms-field-background-color: #ffffff;
        --wpforms-field-border-color: rgba(0, 0, 0, 0.25);
        --wpforms-field-text-color: rgba(0, 0, 0, 0.7);
        --wpforms-label-color: rgba(0, 0, 0, 0.85);
        --wpforms-label-sublabel-color: rgba(0, 0, 0, 0.55);
        --wpforms-label-error-color: #d63637;
        --wpforms-button-border-radius: 3px;
        --wpforms-button-background-color: #066aab;
        --wpforms-button-text-color: #ffffff;
        --wpforms-field-size-input-height: 43px;
        --wpforms-field-size-input-spacing: 15px;
        --wpforms-field-size-font-size: 16px;
        --wpforms-field-size-line-height: 19px;
        --wpforms-field-size-padding-h: 14px;
        --wpforms-field-size-checkbox-size: 16px;
        --wpforms-field-size-sublabel-spacing: 5px;
        --wpforms-field-size-icon-size: 1;
        --wpforms-label-size-font-size: 16px;
        --wpforms-label-size-line-height: 19px;
        --wpforms-label-size-sublabel-font-size: 14px;
        --wpforms-label-size-sublabel-line-height: 17px;
        --wpforms-button-size-font-size: 17px;
        --wpforms-button-size-height: 41px;
        --wpforms-button-size-padding-h: 15px;
        --wpforms-button-size-margin-top: 10px;
      }
    </style>
  </head>
  <body
    class="home page-template-default page page-id-23 theme-hara woocommerce-no-js woo-variation-swatches wvs-behavior-blur wvs-theme-hara-child wvs-show-label wvs-tooltip tbay-homepage-demo tbay-search-mb home-1 tbay-home tbay-show-cart-mobile tbay-body-mobile-product-two elementor-default elementor-kit-6 elementor-page elementor-page-23 woocommerce tbay-variation-free ajax_cart_popup mobile-show-footer-desktop mobile-show-footer-icon"
  >
    <div id="wrapper-container" class="wrapper-container">
      <div
        id="tbay-mobile-smartmenu"
        data-title="Menu"
        class="tbay-mmenu d-xl-none"
      >
        <div class="tbay-offcanvas-body">
          <div id="mmenu-close">
            <button
              type="button"
              class="btn btn-toggle-canvas"
              data-toggle="offcanvas"
            >
              <i class="fas fa-times"></i>
            </button>
          </div>

          <nav
            id="tbay-mobile-menu-navbar"
            class="menu navbar navbar-offcanvas navbar-static"
            data-id="menu-mobile-new"
          >
            <div id="main-mobile-menu-mmenu" class="menu-mobile-new-container">
              <ul
                id="main-mobile-menu-mmenu-wrapper"
                class="menu"
                data-id="mobile-new"
              >
                <li
                  id="menu-item-6696"
                  class="menu-item menu-item-type-post_type menu-item-object-page menu-item-home current-menu-item page_item page-item-23 current_page_item menu-item-6696"
                >
                  <a class="elementor-item" href="{{url('/')}}"
                    ><span class="menu-title">Home</span></a
                  >
                </li>
                <li
                  id="menu-item-6697"
                  class="menu-item menu-item-type-post_type menu-item-object-page menu-item-6697"
                >
                  <a
                    class="elementor-item"
                    href="#"
                    ><span class="menu-title">About Us</span></a
                  >
                </li>
                <li
                  id="menu-item-6695"
                  class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-children menu-item-6695"
                >
                  <a class="elementor-item" href="#"
                    ><span class="menu-title">Categories</span
                    ><b class="caret"></b
                  ></a>
                  <ul class="sub-menu">
                    <li
                      id="menu-item-6700"
                      class="menu-item menu-item-type-taxonomy menu-item-object-product_cat menu-item-6700"
                    >
                      <a
                        class="elementor-item"
                        href="#"
                        ><span class="menu-title">Disposable Brushes</span></a
                      >
                    </li>
                    <li
                      id="menu-item-6701"
                      class="menu-item menu-item-type-taxonomy menu-item-object-product_cat menu-item-has-children menu-item-6701"
                    >
                      <a
                        class="elementor-item"
                        href="#"
                        ><span class="menu-title">Makeup Brushes</span
                        ><b class="caret"></b
                      ></a>
                      <ul class="sub-menu">
                        <li
                          id="menu-item-6702"
                          class="menu-item menu-item-type-taxonomy menu-item-object-product_cat menu-item-6702"
                        >
                          <a
                            class="elementor-item"
                            href="#"
                            ><span class="menu-title">Brush Set</span></a
                          >
                        </li>
                        <li
                          id="menu-item-6703"
                          class="menu-item menu-item-type-taxonomy menu-item-object-product_cat menu-item-6703"
                        >
                          <a
                            class="elementor-item"
                            href="#"
                            ><span class="menu-title"
                              >Classic Collection</span
                            ></a
                          >
                        </li>
                        <li
                          id="menu-item-6704"
                          class="menu-item menu-item-type-taxonomy menu-item-object-product_cat menu-item-6704"
                        >
                          <a
                            class="elementor-item"
                            href="#"
                            ><span class="menu-title"
                              >Metallic Collection</span
                            ></a
                          >
                        </li>
                        <li
                          id="menu-item-6705"
                          class="menu-item menu-item-type-taxonomy menu-item-object-product_cat menu-item-6705"
                        >
                          <a
                            class="elementor-item"
                            href="#"
                            ><span class="menu-title"
                              >Professional Brushes</span
                            ></a
                          >
                        </li>
                        <li
                          id="menu-item-6706"
                          class="menu-item menu-item-type-taxonomy menu-item-object-product_cat menu-item-6706"
                        >
                          <a
                            class="elementor-item"
                            href="#"
                            ><span class="menu-title">Single Brush</span></a
                          >
                        </li>
                      </ul>
                    </li>
                    <li
                      id="menu-item-6707"
                      class="menu-item menu-item-type-taxonomy menu-item-object-product_cat menu-item-6707"
                    >
                      <a
                        class="elementor-item"
                        href="#"
                        ><span class="menu-title"
                          >Sponges &amp; Applicators</span
                        ></a
                      >
                    </li>
                  </ul>
                </li>
                <li
                  id="menu-item-6699"
                  class="menu-item menu-item-type-post_type menu-item-object-page menu-item-6699"
                >
                  <a
                    class="elementor-item"
                    href="#"
                    ><span class="menu-title">FAQ</span></a
                  >
                </li>
                <li
                  id="menu-item-6698"
                  class="menu-item menu-item-type-post_type menu-item-object-page menu-item-6698"
                >
                  <a
                    class="elementor-item"
                    href="#"
                    ><span class="menu-title">Contact Us</span></a
                  >
                </li>
              </ul>
            </div>
          </nav>
        </div>
      </div>
      <div class="topbar-device-mobile d-xl-none clearfix">
        <div class="active-mobile">
          <a href="#tbay-mobile-menu-navbar" class="btn btn-sm"
            ><i class="fas fa-bars"></i></a
          ><a href="#page" class="btn btn-sm"
            ><i class="fas fa-times"></i
          ></a>
        </div>
        <div class="mobile-logo">
          <a href="{{url('/')}}"
            ><img
              src="data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20viewBox='0%200%201200%20579'%3E%3C/svg%3E"
              width="1200"
              height="579"
              alt="Ziba"
              data-lazy-src="https://zibabeauty.in/wp-content/uploads/2021/10/ZibaLogo-for-website-white.png" /><noscript
              ><img
                src="https://zibabeauty.in/wp-content/uploads/2021/10/ZibaLogo-for-website-white.png"
                width="1200"
                height="579"
                alt="Ziba" /></noscript
          ></a>
        </div>
        <div class="device-mini_cart top-cart tbay-element-mini-cart">
          <div
            class="tbay-offcanvas-cart sidebar-right offcanvas offcanvas-end"
            id="cart-offcanvas-mobile"
          >
            <div class="offcanvas-header widget-header-cart">
              <div class="header-cart-content">
                <h3 class="widget-title heading-title">Shopping cart</h3>
                <a
                  href="javascript:;"
                  class="offcanvas-close"
                  data-bs-dismiss="offcanvas"
                  aria-label="Close"
                  ><i class="fas fa-times"></i
                ></a>
              </div>
            </div>
            <div class="offcanvas-body widget_shopping_cart_content">
              <div class="mini_cart_content">
                <div class="mini_cart_inner">
                  <div class="mcart-border">
                    <ul class="cart_empty">
                      <li><span>Your cart is empty</span></li>
                      <li class="total">
                        <a
                          class="button wc-continue"
                          href="#"
                          >Continue Shopping<i
                            class="fas fa-angle-right"
                          ></i
                        ></a>
                      </li>
                    </ul>

                    <div class="clearfix"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="tbay-topcart">
            <div id="cart-RReCT" class="cart-dropdown dropdown">
              <a
                class="dropdown-toggle mini-cart v2"
                data-bs-toggle="offcanvas"
                data-bs-target="#cart-offcanvas-mobile"
                aria-controls="cart-offcanvas-mobile"
                href="javascript:void(0);"
              >
                <i class="fas fa-shopping-cart"></i>
                <span class="mini-cart-items"> 0 </span>
                <span>Cart</span>
              </a>
            </div>
          </div>
        </div>
        <div class="search-device">
          <div class="tbay-search-form tbay-search-mobile">
            <form
              action="#"
              method="get"
              data-parents=".topbar-device-mobile"
              class="searchform hara-ajax-search show-category"
              data-appendto=".search-results-1nVSh"
              data-subtitle="1"
              data-thumbnail="1"
              data-price="1"
              data-minChars="2"
              data-post-type="product"
              data-count="5"
            >
              <div class="form-group">
                <div class="input-group">
                  <div class="select-category input-group-addon">
                    <select
                      name="product_cat"
                      id="product-cat-1nVSh"
                      class="dropdown_product_cat"
                    >
                      <option value="" selected="selected">All</option>
                      <option class="level-0" value="disposable-brushes">
                        Disposable Brushes&nbsp;&nbsp;(3)
                      </option>
                      <option class="level-0" value="makeup-brushes">
                        Makeup Brushes&nbsp;&nbsp;(73)
                      </option>
                      <option class="level-1" value="brush-set">
                        &nbsp;&nbsp;&nbsp;Brush Set&nbsp;&nbsp;(12)
                      </option>
                      <option class="level-1" value="classic-collection">
                        &nbsp;&nbsp;&nbsp;Classic Collection&nbsp;&nbsp;(41)
                      </option>
                      <option class="level-1" value="metallic-collection">
                        &nbsp;&nbsp;&nbsp;Metallic Collection&nbsp;&nbsp;(7)
                      </option>
                      <option class="level-1" value="professional-brushes">
                        &nbsp;&nbsp;&nbsp;Professional Brushes&nbsp;&nbsp;(25)
                      </option>
                      <option class="level-1" value="single-brush">
                        &nbsp;&nbsp;&nbsp;Single Brush&nbsp;&nbsp;(60)
                      </option>
                      <option class="level-0" value="sponges-applicators">
                        Sponges &amp; Applicators&nbsp;&nbsp;(8)
                      </option>
                    </select>
                  </div>
                  <div class="button-group input-group-addon">
                    <button type="submit" class="button-search btn btn-sm>">
                      <i aria-hidden="true" class="fas fa-search"></i>
                    </button>
                    <div class="tbay-preloader"></div>
                  </div>
                  <input
                    data-style="right"
                    type="text"
                    placeholder="Search "
                    name="s"
                    required
                    oninvalid="this.setCustomValidity('Enter at least 2 characters')"
                    oninput="setCustomValidity('')"
                    class="tbay-search form-control input-sm"
                  />

                  <div class="search-results-wrapper">
                    <div
                      class="hara-search-results search-results-1nVSh"
                      data-ajaxsearch="1"
                      data-price="1"
                    ></div>
                  </div>
                  <input
                    type="hidden"
                    name="post_type"
                    value="product"
                    class="post_type"
                  />
                </div>
              </div>
            </form>
            <div id="search-mobile-nav-cover"></div>
          </div>
        </div>
      </div>

      <div class="footer-device-mobile d-xl-none clearfix">
        <div class="list-menu-icon">
          <div class="menu-icon">
            <a title="Home" class="home active" href="{{url('/')}}"
              ><span class="menu-icon-child"
                ><i class="fas fa-home"></i><span>Home</span></span
              ></a
            >
          </div>
          <div class="menu-icon">
            <a
              title="Shop"
              class="shop"
              href="#"
              ><span class="menu-icon-child"
                ><i class="fas fa-store"></i><span>Shop</span></span
              ></a
            >
          </div>
          <div class="menu-icon">
            <a
              title="Checkout"
              class="checkout"
              href="#"
              ><span class="menu-icon-child"
                ><i class="fas fa-credit-card"></i
                ><span>Checkout</span></span
              ></a
            >
          </div>
          <div class="menu-icon">
            <a
              title="Wishlist"
              class="wishlist"
              href="#"
              ><span class="menu-icon-child"
                ><i class="fas fa-heart"></i
                ><span class="count count_wishlist"><span>0</span></span
                ><span>Wishlist</span></span
              ></a
            >
          </div>
          <div class="menu-icon">
            <a
              title="Account"
              class="account"
              href="#"
              ><span class="menu-icon-child"
                ><i class="fas fa-user"></i
                ><span>Account</span></span
              ></a
            >
          </div>
        </div>
      </div>

      <header id="tbay-header" class="tbay_header-template site-header">
        {{-- <style>
          .elementor-51
            .elementor-element.elementor-element-c92871a:not(.elementor-motion-effects-element-type-background),
          .elementor-51
            .elementor-element.elementor-element-c92871a
            > .elementor-motion-effects-container
            > .elementor-motion-effects-layer {
            background-color: #1d1c1c;
          }
          .elementor-51 .elementor-element.elementor-element-c92871a {
            transition: background 0.3s, border 0.3s, border-radius 0.3s,
              box-shadow 0.3s;
            margin-top: 0px;
            margin-bottom: 0px;
            padding: 6px 6px 6px 6px;
          }
          .elementor-51
            .elementor-element.elementor-element-c92871a
            > .elementor-background-overlay {
            transition: background 0.3s, border-radius 0.3s, opacity 0.3s;
          }
          .elementor-51 .elementor-element.elementor-element-8995130 {
            text-align: center;
          }
          .elementor-51
            .elementor-element.elementor-element-8995130
            .elementor-heading-title {
            color: #ffffff;
            font-size: 16px;
          }
          .elementor-51
            .elementor-element.elementor-element-73fc734
            > .elementor-container
            > .elementor-column
            > .elementor-widget-wrap {
            align-content: center;
            align-items: center;
          }
          .elementor-51
            .elementor-element.elementor-element-73fc734:not(.elementor-motion-effects-element-type-background),
          .elementor-51
            .elementor-element.elementor-element-73fc734
            > .elementor-motion-effects-container
            > .elementor-motion-effects-layer {
            background-color: #2c2c2c;
          }
          .elementor-51 .elementor-element.elementor-element-73fc734 {
            border-style: solid;
            border-width: 0px 0px 1px 0px;
            border-color: #ffffff2e;
            transition: background 0.3s, border 0.3s, border-radius 0.3s,
              box-shadow 0.3s;
            padding: 16px 0px 15px 0px;
          }
          .elementor-51
            .elementor-element.elementor-element-73fc734
            > .elementor-background-overlay {
            transition: background 0.3s, border-radius 0.3s, opacity 0.3s;
          }
          .elementor-51 .elementor-element.elementor-element-9d6c1d4 img {
            max-width: 148px;
          }
          .elementor-51
            .elementor-element.elementor-element-43cd067
            .elementor-nav-menu {
            justify-content: center !important;
          }
          .elementor-51
            .elementor-element.elementor-element-43cd067
            .tbay-horizontal
            .navbar-nav
            > li
            > a:after {
            display: none !important;
          }
          .elementor-51
            .elementor-element.elementor-element-43cd067
            .elementor-nav-menu--main.tbay-horizontal
            > ul
            > li
            > a {
            color: #ffffff;
          }
          .elementor-51
            .elementor-element.elementor-element-43cd067
            .elementor-nav-menu--main.tbay-horizontal
            > ul
            > li
            > a
            i {
            color: #ffffff;
          }
          .elementor-51
            .elementor-element.elementor-element-43cd067
            .elementor-nav-menu--main.tbay-horizontal
            > ul
            > li
            > .caret:before {
            background-color: #ffffff;
          }
          .elementor-51
            .elementor-element.elementor-element-43cd067
            .elementor-nav-menu--main.tbay-horizontal
            > ul
            > li
            > a:hover,
          .elementor-51
            .elementor-element.elementor-element-43cd067
            .tbay-element-nav-menu
            .elementor-nav-menu--main.tbay-horizontal
            > ul
            > li:hover
            > a
            > .caret,
          .elementor-51
            .elementor-element.elementor-element-43cd067
            .tbay-element-nav-menu
            .elementor-nav-menu--main.tbay-horizontal
            > ul
            > li:focus
            > a
            > .caret,
          .elementor-51
            .elementor-element.elementor-element-43cd067
            .tbay-element-nav-menu
            .elementor-nav-menu--main.tbay-horizontal
            > ul
            > li.current-menu-parent
            > a
            > .caret,
          .elementor-51
            .elementor-element.elementor-element-43cd067
            .elementor-nav-menu--main.tbay-horizontal
            > ul
            > li
            > a:hover
            i,
          .elementor-51
            .elementor-element.elementor-element-43cd067
            .elementor-nav-menu--main.tbay-horizontal
            > ul
            > li
            > a:focus
            i,
          .elementor-51
            .elementor-element.elementor-element-43cd067
            .elementor-nav-menu--main.tbay-horizontal
            > ul
            > li
            > a.active
            i,
          .elementor-51
            .elementor-element.elementor-element-43cd067
            .elementor-nav-menu--main.tbay-horizontal
            > ul
            > li
            > a:focus,
          .elementor-51
            .elementor-element.elementor-element-43cd067
            .elementor-nav-menu--main.tbay-horizontal
            > ul
            > li.current-menu-parent
            > a,
          .elementor-51
            .elementor-element.elementor-element-43cd067
            .tbay-element-nav-menu
            .elementor-nav-menu--main.tbay-horizontal
            > ul
            > li.current-menu-item
            > a,
          .elementor-51
            .elementor-element.elementor-element-43cd067
            .elementor-nav-menu--main.tbay-horizontal
            > ul
            > li.current_page_item
            > a,
          .elementor-51
            .elementor-element.elementor-element-43cd067
            .elementor-nav-menu--main.tbay-horizontal
            > ul
            > li.current_page_parent
            > a {
            color: #ffffff !important;
          }
          .elementor-51
            .elementor-element.elementor-element-43cd067
            .elementor-nav-menu--main
            .elementor-item {
            padding: 26px 0px 26px 0px;
          }
          .elementor-51
            .elementor-element.elementor-element-43cd067
            .elementor-nav-menu--main
            .dropdown-menu
            .elementor-item {
            padding: 0;
          }
          .elementor-51 .elementor-element.elementor-element-43cd067 {
            width: var(--container-widget-width, 106.424%);
            max-width: 106.424%;
            --container-widget-width: 106.424%;
            --container-widget-flex-grow: 0;
          }
          .elementor-51
            .elementor-element.elementor-element-e6161df
            > div.elementor-element-populated {
            padding: 0px 0px 0px 18px !important;
          }
          .elementor-51
            .elementor-element.elementor-element-b54d16e.elementor-column
            > .elementor-widget-wrap {
            justify-content: flex-end;
          }
          .elementor-51
            .elementor-element.elementor-element-b54d16e
            > div.elementor-element-populated {
            padding: 0px 18px 0px 0px !important;
          }
          .elementor-51
            .elementor-element.elementor-element-74a6cf1
            .cart-dropdown
            .cart-icon {
            color: #ffffff;
            background-color: #02010100;
          }
          .elementor-51
            .elementor-element.elementor-element-74a6cf1
            .cart-dropdown
            .cart-icon:hover {
            color: #ffffff;
          }
          .elementor-51
            .elementor-element.elementor-element-74a6cf1
            .cart-icon
            span.mini-cart-items {
            font-size: 12px;
            font-weight: 400;
          }
          .elementor-51
            .elementor-element.elementor-element-74a6cf1
            .cart-popup
            .dropdown-menu.show {
            inset: 51px 0px auto auto !important;
          }
          .rtl
            .elementor-51
            .elementor-element.elementor-element-74a6cf1
            .cart-popup
            .dropdown-menu.show {
            inset: 51px auto auto 0px !important;
          }
          .elementor-51
            .elementor-element.elementor-element-41757a2
            .tbay-login
            a
            i {
            font-size: 20px !important;
            color: #ffffff;
          }
          .elementor-51
            .elementor-element.elementor-element-41757a2
            .tbay-login
            > a:hover
            i {
            color: #ffffff;
          }
          .elementor-51
            .elementor-element.elementor-element-baae983
            .top-wishlist
            i {
            color: #ffffff;
          }
          .elementor-51
            .elementor-element.elementor-element-baae983
            .top-wishlist
            a:hover
            i {
            color: #ffffff;
          }
          .elementor-51
            .elementor-element.elementor-element-baae983
            .top-wishlist
            .count_wishlist {
            font-size: 12px;
            font-weight: 400;
          }
          @media (min-width: 768px) {
            .elementor-51 .elementor-element.elementor-element-b88b4aa {
              width: 19.912%;
            }
            .elementor-51 .elementor-element.elementor-element-a61c64d {
              width: 57.749%;
            }
            .elementor-51 .elementor-element.elementor-element-e6161df {
              width: 5.149%;
            }
            .elementor-51 .elementor-element.elementor-element-b54d16e {
              width: 17.156%;
            }
          }
        </style> --}}
        <div
          data-elementor-type="wp-post"
          data-elementor-id="51"
          class="elementor elementor-51"
        >
          <section
            class="elementor-section elementor-top-section elementor-element elementor-element-c92871a elementor-section-boxed elementor-section-height-default elementor-section-height-default"
            data-id="c92871a"
            data-element_type="section"
            data-settings='{"background_background":"classic"}'
          >
            <div class="elementor-container elementor-column-gap-default">
              <div
                class="elementor-column elementor-col-100 elementor-top-column elementor-element elementor-element-0980e8e"
                data-id="0980e8e"
                data-element_type="column"
              >
                <div class="elementor-widget-wrap elementor-element-populated">
                  <div
                    class="elementor-element elementor-element-8995130 elementor-widget elementor-widget-heading"
                    data-id="8995130"
                    data-element_type="widget"
                    data-widget_type="heading.default"
                  >
                    <div class="elementor-widget-container">
                      <style>
                        /*! elementor - v3.14.0 - 26-06-2023 */
                        .elementor-heading-title {
                          padding: 0;
                          margin: 0;
                          line-height: 1;
                        }
                        .elementor-widget-heading
                          .elementor-heading-title[class*="elementor-size-"]
                          > a {
                          color: inherit;
                          font-size: inherit;
                          line-height: inherit;
                        }
                        .elementor-widget-heading
                          .elementor-heading-title.elementor-size-small {
                          font-size: 15px;
                        }
                        .elementor-widget-heading
                          .elementor-heading-title.elementor-size-medium {
                          font-size: 19px;
                        }
                        .elementor-widget-heading
                          .elementor-heading-title.elementor-size-large {
                          font-size: 29px;
                        }
                        .elementor-widget-heading
                          .elementor-heading-title.elementor-size-xl {
                          font-size: 39px;
                        }
                        .elementor-widget-heading
                          .elementor-heading-title.elementor-size-xxl {
                          font-size: 59px;
                        }
                      </style>
                      <h6
                        class="elementor-heading-title elementor-size-default"
                      >
                        Free shipping on orders above 499 INR.
                      </h6>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </section>
          <section
            class="elementor-section elementor-top-section elementor-element elementor-element-73fc734 elementor-section-content-middle elementor-section-boxed elementor-section-height-default elementor-section-height-default"
            data-id="73fc734"
            data-element_type="section"
            data-settings='{"background_background":"classic"}'
          >
            <div class="elementor-container elementor-column-gap-default">
              <div
                class="elementor-column elementor-col-25 elementor-top-column elementor-element elementor-element-b88b4aa"
                data-id="b88b4aa"
                data-element_type="column"
              >
                <div class="elementor-widget-wrap elementor-element-populated">
                  <div
                    class="elementor-element elementor-element-9d6c1d4 elementor-widget elementor-widget-hara-site-logo w-auto elementor-widget-tbay-base"
                    data-id="9d6c1d4"
                    data-element_type="widget"
                    data-widget_type="hara-site-logo.default"
                  >
                    <div class="elementor-widget-container">
                      <div class="tbay-element tbay-element-site-logo">
                        <div class="header-logo">
                          <a href="{{url('/')}}">
                            <img
                              width="1200"
                              height="579"
                              src="data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20viewBox='0%200%201200%20579'%3E%3C/svg%3E"
                              class="header-logo-img"
                              alt="ed"
                              decoding="async"
                              fetchpriority="high"
                              data-lazy-src="https://zibabeauty.in/wp-content/uploads/2021/10/ZibaLogo-for-website-white.png"
                            /><noscript
                              ><img
                                width="1200"
                                height="579"
                                src="https://zibabeauty.in/wp-content/uploads/2021/10/ZibaLogo-for-website-white.png"
                                class="header-logo-img"
                                alt="ed"
                                decoding="async"
                                fetchpriority="high"
                            /></noscript>
                          </a>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div
                class="elementor-column elementor-col-25 elementor-top-column elementor-element elementor-element-a61c64d"
                data-id="a61c64d"
                data-element_type="column"
              >
                <div class="elementor-widget-wrap elementor-element-populated">
                  <div
                    class="elementor-element elementor-element-43cd067 elementor-nav-menu__align-center hidden-indicator-yes elementor-widget__width-initial elementor-widget elementor-widget-tbay-nav-menu"
                    data-id="43cd067"
                    data-element_type="widget"
                    data-settings='{"layout":"horizontal"}'
                    data-widget_type="tbay-nav-menu.default"
                  >
                    <div class="elementor-widget-container">
                      <div
                        class="tbay-element tbay-element-nav-menu"
                        data-wrapper='{"layout":"horizontal","type_menu":null}'
                      >
                        <nav
                          class="elementor-nav-menu--main elementor-nav-menu__container elementor-nav-menu--layout-horizontal tbay-horizontal"
                          data-id="main-menu"
                        >
                          <ul
                            id="menu-1-P0nOp"
                            class="elementor-nav-menu menu nav navbar-nav megamenu flex-row"
                          >
                            <li
                              id="menu-item-6429"
                              class="menu-item menu-item-type-post_type menu-item-object-page menu-item-home current-menu-item page_item page-item-23 current_page_item menu-item-6429"
                            >
                              <a
                                class="elementor-item"
                                href="{{url('/')}}"
                                ><span class="menu-title">Home</span></a
                              >
                            </li>
                            <li
                              id="menu-item-6430"
                              class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-children menu-item-6430"
                            >
                              <a class="elementor-item" href="#"
                                ><span class="menu-title">Product Categories</span
                                ><b class="caret"></b
                              ></a>
                              <ul class="sub-menu">
                                @foreach($cate as $item)
                                <li class="menu-item">
                                    <a class="elementor-item" href="{{ url('/category/'.$item->id) }}">
                                        <span class="menu-title">{{ $item->name }}</span>
                                    </a>
                                </li>
                            @endforeach

                              </ul>
                              
                            </li>
                            <li
                              id="menu-item-6597"
                              class="menu-item menu-item-type-post_type menu-item-object-page menu-item-6597"
                            >
                              <a
                                class="elementor-item"
                                href="#"
                                ><span class="menu-title">Shop</span></a
                              >
                            </li>
                            <li
                              id="menu-item-6434"
                              class="menu-item menu-item-type-post_type menu-item-object-page menu-item-6434"
                            >
                              <a
                                class="elementor-item"
                                href="#"
                                ><span class="menu-title">Contact Us</span></a
                              >
                            </li>
                            <li
                              id="menu-item-6590"
                              class="menu-item menu-item-type-custom menu-item-object-custom menu-item-6590"
                            >
                              <a
                                class="elementor-item"
                                href="#"
                                ><span class="menu-title"
                                  >Track My Order</span
                                ></a
                              >
                            </li>
                          </ul>
                        </nav>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div
                class="elementor-column elementor-col-25 elementor-top-column elementor-element elementor-element-e6161df"
                data-id="e6161df"
                data-element_type="column"
              >
                <div class="elementor-widget-wrap"></div>
              </div>
              <div
                class="elementor-column elementor-col-25 elementor-top-column elementor-element elementor-element-b54d16e"
                data-id="b54d16e"
                data-element_type="column"
              >
                <div class="elementor-widget-wrap elementor-element-populated">
                  <div
                    class="elementor-element elementor-element-74a6cf1 w-auto elementor-widget w-auto elementor-widget-tbay-mini-cart"
                    data-id="74a6cf1"
                    data-element_type="widget"
                    data-widget_type="tbay-mini-cart.default"
                  >
                    <div class="elementor-widget-container">
                      <div class="tbay-element tbay-element-mini-cart">
                        <div class="tbay-topcart popup">
                          <div
                            id="cart-FzuqY"
                            class="cart-dropdown cart-popup dropdown"
                          >
                            <a
                              class="dropdown-toggle mini-cart"
                              data-bs-toggle="dropdown"
                              data-bs-auto-close="outside"
                              href="javascript:void(0);"
                              title="View your shopping cart"
                            >
                              <span class="cart-icon">
                                <i class="fas fa-shopping-cart"></i>
                                <span class="mini-cart-items"> 0 </span>
                              </span>
                            </a>
                            <div class="dropdown-menu">
                              <div class="widget_shopping_cart_content">
                                <div class="mini_cart_content">
                                  <div class="mini_cart_inner">
                                    <div class="mcart-border">
                                      <ul class="cart_empty">
                                        <li><span>Your cart is empty</span></li>
                                        <li class="total">
                                          <a
                                            class="button wc-continue"
                                            href="#"
                                            >Continue Shopping<i
                                              class="fas fa-angle-right"
                                            ></i
                                          ></a>
                                        </li>
                                      </ul>

                                      <div class="clearfix"></div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div
                    class="elementor-element elementor-element-41757a2 w-auto elementor-widget w-auto elementor-widget-tbay-account"
                    data-id="41757a2"
                    data-element_type="widget"
                    data-widget_type="tbay-account.default"
                  >
                    <div class="elementor-widget-container">
                      <div
                        class="tbay-element tbay-element-account header-icon"
                      >
                        <div class="tbay-login">
                          <a
                            data-bs-toggle="modal"
                            data-bs-target="#custom-login-wrapper"
                            href="javascript:void(0)"
                          >
                            <i
                              aria-hidden="true"
                              class="fas fa-user"
                            ></i>
                          </a>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div
                    class="elementor-element elementor-element-baae983 w-auto elementor-widget w-auto elementor-widget-tbay-wishlist"
                    data-id="baae983"
                    data-element_type="widget"
                    data-widget_type="tbay-wishlist.default"
                  >
                    <div class="elementor-widget-container">
                      <div
                        class="tbay-element tbay-element-wishlist top-wishlist header-icon"
                      >
                        <a
                          href="#"
                          class="wishlist"
                        >
                          <i
                            aria-hidden="true"
                            class="fas fa-heart"
                          ></i>
                          <span class="count_wishlist"><span>0</span></span>
                        </a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </section>
        </div>

        <div id="nav-cover"></div>
        <div class="bg-close-canvas-menu"></div>
      </header>