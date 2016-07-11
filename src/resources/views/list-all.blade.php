@extends('spark::layouts.app')
@section('content')

    <script>
        /*global ActiveXObject, window, console, define, module, jQuery */
        //jshint unused:false, strict: false

        /*
         PDFObject v2.0.20160414
         https://github.com/pipwerks/PDFObject
         Copyright (c) 2008-2016 Philip Hutchison
         MIT-style license: http://pipwerks.mit-license.org/
         UMD module pattern from https://github.com/umdjs/umd/blob/master/templates/returnExports.js
         */

        (function (root, factory) {
            if (typeof define === 'function' && define.amd) {
                // AMD. Register as an anonymous module.
                define([], factory);
            } else if (typeof module === 'object' && module.exports) {
                // Node. Does not work with strict CommonJS, but
                // only CommonJS-like environments that support module.exports,
                // like Node.
                module.exports = factory();
            } else {
                // Browser globals (root is window)
                root.PDFObject = factory();
            }
        }(this, function () {

            return (function () {

                "use strict";
                //jshint unused:true

                var pdfobjectversion = "2.0.20160414",
                        supportsPDFs,

                //declare functions
                        createAXO,
                        isIE,
                        supportsPdfMimeType = (typeof navigator.mimeTypes['application/pdf'] !== "undefined"),
                        supportsPdfActiveX,
                        buildQueryString,
                        log,
                        embedError,
                        embed,
                        getTargetElement,
                        generatePDFJSiframe,
                        isIOS = (function () {
                            return (/iphone|ipad|ipod/i.test(navigator.userAgent.toLowerCase()));
                        })(),
                        generateEmbedElement;


                /* ----------------------------------------------------
                 Supporting functions
                 ---------------------------------------------------- */

                createAXO = function (type) {
                    var ax;
                    try {
                        ax = new ActiveXObject(type);
                    } catch (e) {
                        ax = null; //ensure ax remains null
                    }
                    return ax;
                };

                //IE11 still uses ActiveX for Adobe Reader, but IE 11 doesn't expose
                //window.ActiveXObject the same way previous versions of IE did
                //window.ActiveXObject will evaluate to false in IE 11, but "ActiveXObject" in window evaluates to true
                //so check the first one for older IE, and the second for IE11
                //FWIW, MS Edge (replacing IE11) does not support ActiveX at all, both will evaluate false
                //Constructed as a method (not a prop) to avoid unneccesarry overhead -- will only be evaluated if needed
                isIE = function () {
                    return !!(window.ActiveXObject || "ActiveXObject" in window);
                };

                //If either ActiveX support for "AcroPDF.PDF" or "PDF.PdfCtrl" are found, return true
                //Constructed as a method (not a prop) to avoid unneccesarry overhead -- will only be evaluated if needed
                supportsPdfActiveX = function () {
                    return !!(createAXO("AcroPDF.PDF") || createAXO("PDF.PdfCtrl"));
                };

                //Determines whether PDF support is available
                supportsPDFs = (supportsPdfMimeType || (isIE() && supportsPdfActiveX()));

                //Creating a querystring for using PDF Open parameters when embedding PDF
                buildQueryString = function (pdfParams) {

                    var string = "",
                            prop;

                    if (pdfParams) {

                        for (prop in pdfParams) {
                            if (pdfParams.hasOwnProperty(prop)) {
                                string += encodeURIComponent(prop) + "=" + encodeURIComponent(pdfParams[prop]) + "&";
                            }
                        }

                        //The string will be empty if no PDF Params found
                        if (string) {

                            string = "#" + string;

                            //Remove last ampersand
                            string = string.slice(0, string.length - 1);

                        }

                    }

                    return string;

                };

                log = function (msg) {
                    if (typeof console !== "undefined" && console.log) {
                        console.log("[PDFObject] " + msg);
                    }
                };

                embedError = function (msg) {
                    log(msg);
                    return false;
                };

                getTargetElement = function (targetSelector) {

                    //Default to body for full-browser PDF
                    var targetNode = document.body;

                    //If a targetSelector is specified, check to see whether
                    //it's passing a selector, jQuery object, or an HTML element

                    if (typeof targetSelector === "string") {

                        //Is CSS selector
                        targetNode = document.querySelector(targetSelector);

                    } else if (typeof jQuery !== "undefined" && targetSelector instanceof jQuery && targetSelector.length) {

                        //Is jQuery element. Extract HTML node
                        targetNode = targetSelector.get(0);

                    } else if (typeof targetSelector.nodeType !== "undefined" && targetSelector.nodeType === 1) {

                        //Is HTML element
                        targetNode = targetSelector;

                    }

                    return targetNode;

                };

                generatePDFJSiframe = function (targetNode, url, PDFJS_URL, id) {

                    var querystring = PDFJS_URL + "?file=" + url;
                    var scrollfix = (isIOS) ? "-webkit-overflow-scrolling: touch; overflow-y: scroll; " : "overflow: hidden; ";
                    var iframe = "<div style='" + scrollfix + "position: absolute; top: 0; right: 0; bottom: 0; left: 0;'><iframe  " + id + " src='" + querystring + "' style='border: none; width: 100%; height: 100%;' frameborder='0'></iframe></div>";
                    targetNode.className += " pdfobject-container";
                    targetNode.style.position = "relative";
                    targetNode.style.overflow = "auto";
                    targetNode.innerHTML = iframe;
                    return targetNode.getElementsByTagName("iframe")[0];

                };

                generateEmbedElement = function (targetNode, targetSelector, url, width, height, id) {

                    var style = "";

                    if (targetSelector && targetSelector !== document.body) {
                        style = "width: " + width + "; height: " + height + ";";
                    } else {
                        style = "position: absolute; top: 0; right: 0; bottom: 0; left: 0; width: 100%; height: 100%;";
                    }

                    targetNode.className += " pdfobject-container";
                    targetNode.innerHTML = "<embed " + id + " class='pdfobject' src='" + url + "' type='application/pdf' style='overflow: auto; " + style + "'/>";

                    return targetNode.getElementsByTagName("embed")[0];

                };

                embed = function (url, targetSelector, options) {

                    //Ensure URL is available. If not, exit now.
                    if (typeof url !== "string") {
                        return embedError("URL is not valid");
                    }

                    //If targetSelector is not defined, convert to boolean
                    targetSelector = (typeof targetSelector !== "undefined") ? targetSelector : false;

                    //Ensure options object is not undefined -- enables easier error checking below
                    options = (typeof options !== "undefined") ? options : {};

                    //Get passed options, or set reasonable defaults
                    var id = (options.id && typeof options.id === "string") ? "id='" + options.id + "'" : "",
                            page = (options.page) ? options.page : false,
                            pdfOpenParams = (options.pdfOpenParams) ? options.pdfOpenParams : {},
                            fallbackLink = (typeof options.fallbackLink !== "undefined") ? options.fallbackLink : true,
                            width = (options.width) ? options.width : "100%",
                            height = (options.height) ? options.height : "100%",
                            forcePDFJS = (typeof options.forcePDFJS === "boolean") ? options.forcePDFJS : false,
                            PDFJS_URL = (options.PDFJS_URL) ? options.PDFJS_URL : false,
                            targetNode = getTargetElement(targetSelector),
                            fallbackHTML = "",
                            fallbackHTML_default = "<p>This browser does not support inline PDFs. Please download the PDF to view it: <a href='[url]'>Download PDF</a></p>";
                    //If target element is specified but is not valid, exit without doing anything
                    if (!targetNode) {
                        return embedError("Target element cannot be determined");
                    }
                    //page option overrides pdfOpenParams, if found
                    if (page) {
                        pdfOpenParams.page = page;
                    }
                    //Append optional Adobe params for opening document
                    url = encodeURI(url) + buildQueryString(pdfOpenParams);
                    //Do the dance
                    if (forcePDFJS && PDFJS_URL) {
                        return generatePDFJSiframe(targetNode, url, PDFJS_URL, id);
                    } else if (supportsPDFs) {
                        return generateEmbedElement(targetNode, targetSelector, url, width, height, id);
                    } else {
                        if (PDFJS_URL) {
                            return generatePDFJSiframe(targetNode, url, PDFJS_URL, id);
                        } else if (fallbackLink) {

                            fallbackHTML = (typeof fallbackLink === "string") ? fallbackLink : fallbackHTML_default;
                            targetNode.innerHTML = fallbackHTML.replace(/\[url\]/g, url);
                        }
                        return embedError("This browser does not support embedded PDFs");
                    }
                };
                return {
                    embed: function (a, b, c) {
                        return embed(a, b, c);
                    },
                    pdfobjectversion: (function () {
                        return pdfobjectversion;
                    })(),
                    supportsPDFs: (function () {
                        return supportsPDFs;
                    })()
                };
            })();
        }));
        //
        // http://thecodeabode.blogspot.com
        // @author: Ben Kitzelman
        // @license:  FreeBSD: (http://opensource.org/licenses/BSD-2-Clause) Do whatever you like with it
        // @updated: 03-03-2013
        //
        var getAcrobatInfo = function () {

            var getBrowserName = function () {
                return this.name = this.name || function () {
                            var userAgent = navigator ? navigator.userAgent.toLowerCase() : "other";

                            if (userAgent.indexOf("chrome") > -1)        return "chrome";
                            else if (userAgent.indexOf("safari") > -1)   return "safari";
                            else if (userAgent.indexOf("msie") > -1)     return "ie";
                            else if (userAgent.indexOf("firefox") > -1)  return "firefox";
                            return userAgent;
                        }();
            };

            var getActiveXObject = function (name) {
                try {
                    return new ActiveXObject(name);
                } catch (e) {
                }
            };

            var getNavigatorPlugin = function (name) {
                for (key in navigator.plugins) {
                    var plugin = navigator.plugins[key];
                    if (plugin.name == name) return plugin;
                }
            };

            var getPDFPlugin = function () {
                return this.plugin = this.plugin || function () {
                            if (getBrowserName() == 'ie') {
                                // load the activeX control
                                // AcroPDF.PDF is used by version 7 and later
                                // PDF.PdfCtrl is used by version 6 and earlier
                                return getActiveXObject('AcroPDF.PDF') || getActiveXObject('PDF.PdfCtrl');
                            }
                            else {
                                return getNavigatorPlugin('Adobe Acrobat') || getNavigatorPlugin('Chrome PDF Viewer') || getNavigatorPlugin('WebKit built-in PDF');
                            }
                        }();
            };

            var isAcrobatInstalled = function () {
                return !!getPDFPlugin();
            };

            var getAcrobatVersion = function () {
                try {
                    var plugin = getPDFPlugin();

                    if (getBrowserName() == 'ie') {
                        var versions = plugin.GetVersions().split(',');
                        var latest = versions[0].split('=');
                        return parseFloat(latest[1]);
                    }

                    if (plugin.version) return parseInt(plugin.version);
                    return plugin.name

                }
                catch (e) {
                    return null;
                }
            }

            //
            // The returned object
            //
            return {
                browser: getBrowserName(),
                acrobat: isAcrobatInstalled() ? 'installed' : false,
                acrobatVersion: getAcrobatVersion()
            };
        };
    </script>
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                @include('contractor::shared.menu')
            </div>
            <div class="col-md-8 list-all">
                @foreach($contracts as $contract)

                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <div class="row">
                                <div class='contract-title pull-left'>
                                    {{ $contract->name }}
                                    <div class='contract-with'>
                                        {{ $contract->who_its_through }}
                                    </div>
                                    <div class='contract-time'>
                                        {{ $contract->ended_at->format('F j, Y, g:i a') }}
                                    </div>
                                </div>
                                <div class='pull-right' style="width:8rem;padding:10px" >
                                    <a href='{{ route('contractor::edit', [$contract->id]) }}' class="pull-right">
                                        <i class='material-icons grey-text'>edit</i>
                                    </a>
                                    <form action="{{route('warden::api.delete-model', ['contract',$contract->id])}}"
                                          method='post' class="contract" @submit.prevent="makeRequest" class="pull-left">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="{{csrf_token()}}">
                                        <input type="hidden" name="_redirect"
                                               value="{{ route('contractor::list', str_slug($related_model->name)) }}">
                                        <button type="submit" class="method-button"><i class="material-icons red-text">delete</i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class=" panel-body" style="padding:0;">
                            <p>{{ $contract->description }}</p>
                            <ul >
                                @foreach($contract->paths as $path)
                                    <li class="card">
                                        <div class="" style="height:3rem;font-size: 2rem;">
                                            Contract: {{ $path->uuid }}
                                        </div>
                                        <div class="">
                                            <div id="{{ $path->uuid }}"
                                                 data-data="{{ route('contractor::api.v1.get-document', $path->uuid) }}"
                                                 style="width:100%;min-height:40rem;"></div>
                                            <script>
                                                var contract = document.getElementById('{{  $path->uuid }}')
                                                console.log(getAcrobatInfo().acrobat);
                                                if (getAcrobatInfo().acrobat == "installed") {
                                                    var embed = document.createElement('object');
                                                    embed.data = contract.getAttribute('data-data');
                                                    embed.type = 'application/pdf';
                                                    embed.style = 'width:100%; min-height:40rem;height:40rem;';
                                                    contract.appendChild(embed);
                                                } else {
                                                    //Since adobe isn't installed, we need to use the pdfobject program library.
                                                    PDFObject.embed(contract.getAttribute('data-data'), '#{{ $path->uuid }}')
                                                }
                                            </script>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>
@endsection


