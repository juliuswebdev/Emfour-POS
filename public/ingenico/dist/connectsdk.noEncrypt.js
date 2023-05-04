/* eslint-disable */
(function (global){
  if (typeof define === "undefined") {
    var modules = {};
    global.define = function (module, dependencies, fn) {
      if (dependencies && dependencies.length) {
        for (var i = 0; i < dependencies.length; i++) {
          var dependency = modules[dependencies[i]]
          if (typeof dependency === "undefined" && dependencies[i] !== "require" && dependencies[i] !== "exports") {
            console.warn(`Could not find dependency '${dependencies[i]}' of module '${module}'`)
          }
          dependencies[i] = dependency;
        }
      }
      modules[module] = fn.apply(this, dependencies || []);
    };
    global.define("node-forge", [], function () {
      return global.forge || {};
    })
  }
  if (typeof exports === "undefined") {
    global.define("exports", [], function () {
      return {};
    });
  }
}(typeof window === "undefined" ? this : window));

var __extends = (this && this.__extends) || (function () {
    var extendStatics = function (d, b) {
        extendStatics = Object.setPrototypeOf ||
            ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
            function (d, b) { for (var p in b) if (Object.prototype.hasOwnProperty.call(b, p)) d[p] = b[p]; };
        return extendStatics(d, b);
    };
    return function (d, b) {
        if (typeof b !== "function" && b !== null)
            throw new TypeError("Class extends value " + String(b) + " is not a constructor or null");
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
///<amd-module name="connectsdk.apimodel"/>
define("connectsdk.apimodel", ["require", "exports"], function (require, exports) {
    "use strict";
    exports.__esModule = true;
});
///<amd-module name="connectsdk.LabelTemplateElement"/>
define("connectsdk.LabelTemplateElement", ["require", "exports"], function (require, exports) {
    "use strict";
    var LabelTemplateElement = /** @class */ (function () {
        function LabelTemplateElement(json) {
            this.json = json;
            this.attributeKey = json.attributeKey;
            this.mask = json.mask;
            this.wildcardMask = json.mask ? json.mask.replace(/9/g, "*") : "";
        }
        return LabelTemplateElement;
    }());
    return LabelTemplateElement;
});
///<amd-module name="connectsdk.AccountOnFileDisplayHints"/>
define("connectsdk.AccountOnFileDisplayHints", ["require", "exports", "connectsdk.LabelTemplateElement"], function (require, exports, LabelTemplateElement) {
    "use strict";
    function _parseJSON(_json, _labelTemplate, _labelTemplateElementByAttributeKey) {
        if (_json.labelTemplate) {
            for (var _i = 0, _a = _json.labelTemplate; _i < _a.length; _i++) {
                var element = _a[_i];
                var labelTemplateElement = new LabelTemplateElement(element);
                _labelTemplate.push(labelTemplateElement);
                _labelTemplateElementByAttributeKey[labelTemplateElement.attributeKey] = labelTemplateElement;
            }
        }
    }
    var AccountOnFileDisplayHints = /** @class */ (function () {
        function AccountOnFileDisplayHints(json) {
            this.json = json;
            this.logo = json.logo;
            this.labelTemplate = [];
            this.labelTemplateElementByAttributeKey = {};
            _parseJSON(json, this.labelTemplate, this.labelTemplateElementByAttributeKey);
        }
        return AccountOnFileDisplayHints;
    }());
    return AccountOnFileDisplayHints;
});
///<amd-module name="connectsdk.Attribute"/>
define("connectsdk.Attribute", ["require", "exports"], function (require, exports) {
    "use strict";
    var Attribute = /** @class */ (function () {
        function Attribute(json) {
            this.json = json;
            this.key = json.key;
            this.value = json.value;
            this.status = json.status;
            this.mustWriteReason = json.mustWriteReason;
        }
        return Attribute;
    }());
    return Attribute;
});
///<amd-module name="connectsdk.MaskedString"/>
define("connectsdk.MaskedString", ["require", "exports"], function (require, exports) {
    "use strict";
    var MaskedString = /** @class */ (function () {
        function MaskedString(formattedValue, cursorIndex) {
            this.formattedValue = formattedValue;
            this.cursorIndex = cursorIndex;
        }
        return MaskedString;
    }());
    return MaskedString;
});
///<amd-module name="connectsdk.MaskingUtil"/>
define("connectsdk.MaskingUtil", ["require", "exports", "connectsdk.MaskedString"], function (require, exports, MaskedString) {
    "use strict";
    function _fillBuffer(index, offset, buffer, tempMask, valuec) {
        if (index + offset < valuec.length && index < tempMask.length) {
            if ((tempMask[index] === "9" && Number(valuec[index + offset]) > -1 && valuec[index + offset] !== " ") || tempMask[index] === "*") {
                buffer.push(valuec[index + offset]);
            }
            else {
                if (valuec[index + offset] === tempMask[index]) {
                    buffer.push(valuec[index + offset]);
                }
                else if (tempMask[index] !== "9" && tempMask[index] !== "*") {
                    buffer.push(tempMask[index]);
                    offset--;
                }
                else {
                    // offset++;
                    valuec.splice(index + offset, 1);
                    index--;
                }
            }
            _fillBuffer(index + 1, offset, buffer, tempMask, valuec);
        }
    }
    var MaskingUtil = /** @class */ (function () {
        function MaskingUtil() {
        }
        MaskingUtil.prototype.applyMask = function (mask, newValue, oldValue) {
            var buffer = [];
            var valuec = newValue.split("");
            if (mask) {
                var maskc = mask.split("");
                var tempMask = [];
                for (var _i = 0, maskc_1 = maskc; _i < maskc_1.length; _i++) {
                    var c = maskc_1[_i];
                    // the char '{' and '}' should ALWAYS be ignored
                    if (c === "{" || c === "}") {
                        // ignore
                    }
                    else {
                        tempMask.push(c);
                    }
                }
                // tempmask now contains the replaceable chars and the non-replaceable masks at the correct index
                _fillBuffer(0, 0, buffer, tempMask, valuec);
            }
            else {
                // send back as is
                for (var _a = 0, valuec_1 = valuec; _a < valuec_1.length; _a++) {
                    var c = valuec_1[_a];
                    buffer.push(c);
                }
            }
            newValue = buffer.join("");
            var cursor = 1;
            // calculate the cursor index
            if (oldValue) {
                var tester = oldValue.split("");
                for (var i = 0, il = buffer.length; i < il; i++) {
                    if (buffer[i] !== tester[i]) {
                        cursor = i + 1;
                        break;
                    }
                }
            }
            if (newValue.substring(0, newValue.length - 1) === oldValue) {
                cursor = newValue.length + 1;
            }
            return new MaskedString(newValue, cursor);
        };
        MaskingUtil.prototype.getMaxLengthBasedOnMask = function (mask) {
            if (mask) {
                var maskc = mask.split("");
                var newLength = -1;
                for (var _i = 0, maskc_2 = maskc; _i < maskc_2.length; _i++) {
                    var c = maskc_2[_i];
                    newLength++;
                    if (c === "{" || c === "}") {
                        newLength--;
                    }
                }
                return newLength;
            }
            return -1;
        };
        MaskingUtil.prototype.removeMask = function (mask, value) {
            // remove the mask from the masked input
            var buffer = [];
            var valuec = value ? value.split("") : [];
            if (mask) {
                var maskc = mask.split("");
                var valueIndex = -1;
                var inMask = false;
                for (var _i = 0, maskc_3 = maskc; _i < maskc_3.length; _i++) {
                    var c = maskc_3[_i];
                    valueIndex++;
                    // the char '{' and '}' should ALWAYS be ignored
                    if (c === "{" || c === "}") {
                        valueIndex--;
                        if (c === "{") {
                            inMask = true;
                        }
                        else if (c === "}") {
                            inMask = false;
                        }
                    }
                    else {
                        if (inMask && valuec[valueIndex]) {
                            buffer.push(valuec[valueIndex]);
                        }
                    }
                }
            }
            else {
                // send back as is
                for (var _a = 0, valuec_2 = valuec; _a < valuec_2.length; _a++) {
                    var c = valuec_2[_a];
                    buffer.push(c);
                }
            }
            return buffer.join("").trim();
        };
        return MaskingUtil;
    }());
    return MaskingUtil;
});
///<amd-module name="connectsdk.AccountOnFile"/>
define("connectsdk.AccountOnFile", ["require", "exports", "connectsdk.AccountOnFileDisplayHints", "connectsdk.Attribute", "connectsdk.MaskingUtil"], function (require, exports, AccountOnFileDisplayHints, Attribute, MaskingUtil) {
    "use strict";
    function _parseJSON(_json, _attributes, _attributeByKey) {
        if (_json.attributes) {
            for (var _i = 0, _a = _json.attributes; _i < _a.length; _i++) {
                var attr = _a[_i];
                var attribute = new Attribute(attr);
                _attributes.push(attribute);
                _attributeByKey[attribute.key] = attribute;
            }
        }
    }
    var AccountOnFile = /** @class */ (function () {
        function AccountOnFile(json) {
            this.json = json;
            this.attributes = [];
            this.attributeByKey = {};
            this.displayHints = new AccountOnFileDisplayHints(json.displayHints);
            this.id = json.id;
            this.paymentProductId = json.paymentProductId;
            _parseJSON(json, this.attributes, this.attributeByKey);
        }
        AccountOnFile.prototype.getMaskedValueByAttributeKey = function (attributeKey) {
            var value = this.attributeByKey[attributeKey].value;
            var wildcardMask;
            try {
                wildcardMask = this.displayHints.labelTemplateElementByAttributeKey[attributeKey].wildcardMask;
            }
            catch (e) {
                /* ignore */
            }
            if (value !== undefined && wildcardMask !== undefined) {
                var maskingUtil = new MaskingUtil();
                return maskingUtil.applyMask(wildcardMask, value);
            }
            return undefined;
        };
        return AccountOnFile;
    }());
    return AccountOnFile;
});
///<amd-module name="connectsdk.promise"/>
define("connectsdk.promise", ["require", "exports"], function (require, exports) {
    "use strict";
    /* eslint-disable @typescript-eslint/no-non-null-assertion */
    /**
     * The Promise class.
     */
    var Promise = /** @class */ (function () {
        function Promise(singleton) {
            var _this = this;
            this.isSingleton = singleton || false;
            // eslint-disable-next-line @typescript-eslint/no-explicit-any
            var pending = [];
            /**
             * Runs through each pending 'thenable' based on type (resolve, reject).
             *
             * @param {String} type The thenable type
             * @param {Object} result A value
             */
            // eslint-disable-next-line @typescript-eslint/no-explicit-any
            function complete(type, result) {
                while (pending[0]) {
                    var cb = pending.shift()[type];
                    if (cb) {
                        cb(result);
                    }
                }
            }
            this.resolve = function (result) {
                complete("resolve", result);
            };
            // eslint-disable-next-line @typescript-eslint/no-explicit-any
            this.reject = function (result) {
                complete("reject", result);
            };
            // eslint-disable-next-line @typescript-eslint/no-explicit-any
            this.then = function (success, failure) {
                pending.push({
                    resolve: success,
                    reject: failure
                });
                return _this;
            };
        }
        Promise.resolve = function (value) {
            var promise = new Promise();
            setTimeout(function () { return promise.resolve(value); }, 0);
            return promise;
        };
        // eslint-disable-next-line @typescript-eslint/no-explicit-any
        Promise.reject = function (reason) {
            var promise = new Promise();
            setTimeout(function () { return promise.reject(reason); }, 0);
            return promise;
        };
        return Promise;
    }());
    return Promise;
});
///<amd-module name="connectsdk.types"/>
define("connectsdk.types", ["require", "exports"], function (require, exports) {
    "use strict";
    exports.__esModule = true;
});
///<amd-module name="connectsdk.Util"/>
define("connectsdk.Util", ["require", "exports"], function (require, exports) {
    "use strict";
    var instance;
    function createInstance() {
        return {
            applePayPaymentProductId: 302,
            googlePayPaymentProductId: 320,
            bancontactPaymentProductId: 3012,
            paymentProductsThatAreNotSupportedInThisBrowser: [],
            getMetadata: function () {
                return {
                    screenSize: window.innerWidth + "x" + window.innerHeight,
                    platformIdentifier: window.navigator.userAgent,
                    sdkIdentifier: (document["GC"] && document["GC"].rppEnabledPage ? "rpp-" : "") + "JavaScriptClientSDK/v4.1.0",
                    sdkCreator: "Ingenico"
                };
            },
            collectDeviceInformation: function () {
                return {
                    timezoneOffsetUtcMinutes: new Date().getTimezoneOffset(),
                    locale: navigator.language,
                    browserData: {
                        javaScriptEnabled: true,
                        javaEnabled: navigator.javaEnabled(),
                        colorDepth: screen.colorDepth,
                        screenHeight: screen.height,
                        screenWidth: screen.width,
                        innerHeight: window.innerHeight,
                        innerWidth: window.innerWidth
                    }
                };
            },
            base64Encode: function (data) {
                if (typeof data === "object") {
                    try {
                        data = JSON.stringify(data);
                    }
                    catch (e) {
                        throw "data must be either a String or a JSON object";
                    }
                }
                if (!data) {
                    return data;
                }
                var b64 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
                var i = 0;
                var ac = 0;
                var tmpArr = [];
                do {
                    // pack three octets into four hexets
                    var o1 = data.charCodeAt(i++);
                    var o2 = data.charCodeAt(i++);
                    var o3 = data.charCodeAt(i++);
                    var bits = (o1 << 16) | (o2 << 8) | o3;
                    var h1 = (bits >> 18) & 0x3f;
                    var h2 = (bits >> 12) & 0x3f;
                    var h3 = (bits >> 6) & 0x3f;
                    var h4 = bits & 0x3f;
                    // use hexets to index into b64, and append result to encoded string
                    tmpArr[ac++] = b64.charAt(h1) + b64.charAt(h2) + b64.charAt(h3) + b64.charAt(h4);
                } while (i < data.length);
                var enc = tmpArr.join("");
                var r = data.length % 3;
                return (r ? enc.slice(0, r - 3) : enc) + "===".slice(r || 3);
            },
            filterOutProductsThatAreNotSupportedInThisBrowser: function (json) {
                for (var i = json.paymentProducts.length - 1, il = 0; i >= il; i--) {
                    var product = json.paymentProducts[i];
                    if (product && this.paymentProductsThatAreNotSupportedInThisBrowser.indexOf(product.id) > -1) {
                        json.paymentProducts.splice(i, 1);
                    }
                }
            }
        };
    }
    return {
        getInstance: function () {
            if (!instance) {
                instance = createInstance();
            }
            return instance;
        }
    };
});
///<amd-module name="connectsdk.ApplePay"/>
define("connectsdk.ApplePay", ["require", "exports", "connectsdk.promise", "connectsdk.Util"], function (require, exports, Promise, Util) {
    "use strict";
    var util = Util.getInstance();
    var ApplePay = /** @class */ (function () {
        function ApplePay() {
        }
        ApplePay.prototype.isApplePayAvailable = function () {
            var applePayIsAvailable = window["ApplePaySession"] && window["ApplePaySession"].canMakePayments();
            if (!applePayIsAvailable) {
                util.paymentProductsThatAreNotSupportedInThisBrowser.push(util.applePayPaymentProductId);
            }
            return !!applePayIsAvailable;
        };
        ApplePay.prototype.initPayment = function (context, c2SCommunicator) {
            var promise = new Promise();
            var countryCode = context.acquirerCountry ? context.acquirerCountry : context.countryCode;
            var payment = {
                currencyCode: context.currency,
                countryCode: countryCode,
                total: {
                    label: context.displayName,
                    amount: (context.totalAmount / 100).toString()
                },
                supportedNetworks: context.networks,
                merchantCapabilities: ["supports3DS"]
            };
            var applePaySession = new ApplePaySession(1, payment);
            applePaySession.begin();
            applePaySession.onvalidatemerchant = function (event) {
                var sessionContext = {
                    displayName: context.displayName,
                    validationURL: event.validationURL,
                    domainName: window.location.hostname
                };
                c2SCommunicator.createPaymentProductSession(302, sessionContext).then(function (merchantSession) {
                    try {
                        applePaySession.completeMerchantValidation(JSON.parse(merchantSession.paymentProductSession302SpecificOutput.sessionObject));
                    }
                    catch (e) {
                        promise.reject(e);
                        applePaySession.abort();
                    }
                }, function (errorJSON) {
                    promise.reject(errorJSON);
                    applePaySession.abort();
                });
            };
            applePaySession.onpaymentauthorized = function (event) {
                if (!event.payment.token) {
                    var status_1 = ApplePaySession.STATUS_FAILURE;
                    promise.reject({ message: "Error payment authorization" });
                    applePaySession.completePayment(status_1);
                }
                else {
                    var status_2 = ApplePaySession.STATUS_SUCCESS;
                    promise.resolve({ message: "Payment authorized", data: event.payment.token });
                    applePaySession.completePayment(status_2);
                }
            };
            return promise;
        };
        return ApplePay;
    }());
    return ApplePay;
});
///<amd-module name="connectsdk.AuthenticationIndicator"/>
define("connectsdk.AuthenticationIndicator", ["require", "exports"], function (require, exports) {
    "use strict";
    var AuthenticationIndicator = /** @class */ (function () {
        function AuthenticationIndicator(json) {
            this.json = json;
            this.name = json.name;
            this.value = json.value;
        }
        return AuthenticationIndicator;
    }());
    return AuthenticationIndicator;
});
///<amd-module name="connectsdk.PaymentProduct302SpecificData"/>
define("connectsdk.PaymentProduct302SpecificData", ["require", "exports"], function (require, exports) {
    "use strict";
    var PaymentProduct302SpecificData = /** @class */ (function () {
        function PaymentProduct302SpecificData(json) {
            this.json = json;
            this.networks = json.networks;
        }
        return PaymentProduct302SpecificData;
    }());
    return PaymentProduct302SpecificData;
});
///<amd-module name="connectsdk.PaymentProduct320SpecificData"/>
define("connectsdk.PaymentProduct320SpecificData", ["require", "exports"], function (require, exports) {
    "use strict";
    var PaymentProduct320SpecificData = /** @class */ (function () {
        function PaymentProduct320SpecificData(json) {
            this.json = json;
            this.gateway = json.gateway;
            this.networks = json.networks;
        }
        return PaymentProduct320SpecificData;
    }());
    return PaymentProduct320SpecificData;
});
///<amd-module name="connectsdk.PaymentProduct863SpecificData"/>
define("connectsdk.PaymentProduct863SpecificData", ["require", "exports"], function (require, exports) {
    "use strict";
    var PaymentProduct863SpecificData = /** @class */ (function () {
        function PaymentProduct863SpecificData(json) {
            this.json = json;
            this.integrationTypes = json.integrationTypes;
        }
        return PaymentProduct863SpecificData;
    }());
    return PaymentProduct863SpecificData;
});
///<amd-module name="connectsdk.PaymentProductDisplayHints"/>
define("connectsdk.PaymentProductDisplayHints", ["require", "exports"], function (require, exports) {
    "use strict";
    var PaymentProductDisplayHints = /** @class */ (function () {
        function PaymentProductDisplayHints(json) {
            this.json = json;
            this.displayOrder = json.displayOrder;
            this.label = json.label;
            this.logo = json.logo;
        }
        return PaymentProductDisplayHints;
    }());
    return PaymentProductDisplayHints;
});
///<amd-module name="connectsdk.BasicPaymentProduct"/>
define("connectsdk.BasicPaymentProduct", ["require", "exports", "connectsdk.AccountOnFile", "connectsdk.AuthenticationIndicator", "connectsdk.PaymentProduct302SpecificData", "connectsdk.PaymentProduct320SpecificData", "connectsdk.PaymentProduct863SpecificData", "connectsdk.PaymentProductDisplayHints"], function (require, exports, AccountOnFile, AuthenticationIndicator, PaymentProduct302SpecificData, PaymentProduct320SpecificData, PaymentProduct863SpecificData, PaymentProductDisplayHints) {
    "use strict";
    function _parseJSON(_json, _accountsOnFile, _accountOnFileById) {
        if (_json.accountsOnFile) {
            for (var _i = 0, _a = _json.accountsOnFile; _i < _a.length; _i++) {
                var aof = _a[_i];
                var accountOnFile = new AccountOnFile(aof);
                _accountsOnFile.push(accountOnFile);
                _accountOnFileById[accountOnFile.id] = accountOnFile;
            }
        }
    }
    var BasicPaymentProduct = /** @class */ (function () {
        function BasicPaymentProduct(json) {
            this.json = json;
            this.type = "product";
            this.json.type = "product";
            this.accountsOnFile = [];
            this.accountOnFileById = {};
            this.allowsRecurring = json.allowsRecurring;
            this.allowsTokenization = json.allowsTokenization;
            this.autoTokenized = json.autoTokenized;
            this.allowsInstallments = json.allowsInstallments;
            this.acquirerCountry = json.acquirerCountry;
            this.canBeIframed = json.canBeIframed;
            this.deviceFingerprintEnabled = json.deviceFingerprintEnabled;
            this.displayHints = new PaymentProductDisplayHints(json.displayHints);
            this.id = json.id;
            this.isJavaScriptRequired = json.isJavaScriptRequired;
            this.maxAmount = json.maxAmount;
            this.minAmount = json.minAmount;
            this.paymentMethod = json.paymentMethod;
            this.mobileIntegrationLevel = json.mobileIntegrationLevel;
            this.usesRedirectionTo3rdParty = json.usesRedirectionTo3rdParty;
            this.paymentProductGroup = json.paymentProductGroup;
            this.supportsMandates = json.supportsMandates;
            if (json.authenticationIndicator) {
                this.authenticationIndicator = new AuthenticationIndicator(json.authenticationIndicator);
            }
            if (json.paymentProduct302SpecificData) {
                this.paymentProduct302SpecificData = new PaymentProduct302SpecificData(json.paymentProduct302SpecificData);
            }
            if (json.paymentProduct320SpecificData) {
                this.paymentProduct320SpecificData = new PaymentProduct320SpecificData(json.paymentProduct320SpecificData);
            }
            if (json.paymentProduct863SpecificData) {
                this.paymentProduct863SpecificData = new PaymentProduct863SpecificData(json.paymentProduct863SpecificData);
            }
            _parseJSON(json, this.accountsOnFile, this.accountOnFileById);
        }
        BasicPaymentProduct.prototype.copy = function () {
            var json = JSON.parse(JSON.stringify(this.json));
            return new BasicPaymentProduct(json);
        };
        return BasicPaymentProduct;
    }());
    return BasicPaymentProduct;
});
///<amd-module name="connectsdk.BasicPaymentProductGroup"/>
define("connectsdk.BasicPaymentProductGroup", ["require", "exports", "connectsdk.AccountOnFile", "connectsdk.PaymentProductDisplayHints"], function (require, exports, AccountOnFile, PaymentProductDisplayHints) {
    "use strict";
    function _parseJSON(_json, _accountsOnFile, _accountOnFileById) {
        if (_json.accountsOnFile) {
            for (var _i = 0, _a = _json.accountsOnFile; _i < _a.length; _i++) {
                var aof = _a[_i];
                var accountOnFile = new AccountOnFile(aof);
                _accountsOnFile.push(accountOnFile);
                _accountOnFileById[accountOnFile.id] = accountOnFile;
            }
        }
    }
    var BasicPaymentProductGroup = /** @class */ (function () {
        function BasicPaymentProductGroup(json) {
            this.json = json;
            this.type = "group";
            this.json.type = "group";
            this.id = json.id;
            //this.acquirerCountry = json.acquirerCountry;
            this.allowsInstallments = json.allowsInstallments;
            this.displayHints = new PaymentProductDisplayHints(json.displayHints);
            this.accountsOnFile = [];
            this.accountOnFileById = {};
            _parseJSON(json, this.accountsOnFile, this.accountOnFileById);
        }
        BasicPaymentProductGroup.prototype.copy = function () {
            var json = JSON.parse(JSON.stringify(this.json));
            return new BasicPaymentProductGroup(json);
        };
        return BasicPaymentProductGroup;
    }());
    return BasicPaymentProductGroup;
});
///<amd-module name="connectsdk.BasicPaymentItem"/>
define("connectsdk.BasicPaymentItem", ["require", "exports"], function (require, exports) {
    "use strict";
    exports.__esModule = true;
});
///<amd-module name="connectsdk.BasicPaymentProductGroups"/>
define("connectsdk.BasicPaymentProductGroups", ["require", "exports", "connectsdk.BasicPaymentProductGroup"], function (require, exports, BasicPaymentProductGroup) {
    "use strict";
    function _parseJson(_json, _paymentProductGroups, _accountsOnFile, _paymentProductGroupById, _accountOnFileById) {
        if (_json.paymentProductGroups) {
            for (var _i = 0, _a = _json.paymentProductGroups; _i < _a.length; _i++) {
                var productGroup = _a[_i];
                var paymentProductGroup = new BasicPaymentProductGroup(productGroup);
                _paymentProductGroups.push(paymentProductGroup);
                _paymentProductGroupById[paymentProductGroup.id] = paymentProductGroup;
                if (paymentProductGroup.accountsOnFile) {
                    for (var _b = 0, _c = paymentProductGroup.accountsOnFile; _b < _c.length; _b++) {
                        var aof = _c[_b];
                        _accountsOnFile.push(aof);
                        _accountOnFileById[aof.id] = aof;
                    }
                }
            }
        }
    }
    var BasicPaymentProductGroups = /** @class */ (function () {
        function BasicPaymentProductGroups(json) {
            this.json = json;
            this.basicPaymentProductGroups = [];
            this.basicPaymentProductGroupById = {};
            this.accountsOnFile = [];
            this.accountOnFileById = {};
            _parseJson(json, this.basicPaymentProductGroups, this.accountsOnFile, this.basicPaymentProductGroupById, this.accountOnFileById);
        }
        return BasicPaymentProductGroups;
    }());
    return BasicPaymentProductGroups;
});
///<amd-module name="connectsdk.BasicPaymentProducts"/>
define("connectsdk.BasicPaymentProducts", ["require", "exports", "connectsdk.BasicPaymentProduct"], function (require, exports, BasicPaymentProduct) {
    "use strict";
    function _parseJson(_json, _paymentProducts, _accountsOnFile, _paymentProductById, _accountOnFileById, _paymentProductByAccountOnFileId) {
        if (_json.paymentProducts) {
            for (var _i = 0, _a = _json.paymentProducts; _i < _a.length; _i++) {
                var product = _a[_i];
                var paymentProduct = new BasicPaymentProduct(product);
                _paymentProducts.push(paymentProduct);
                _paymentProductById[paymentProduct.id] = paymentProduct;
                if (paymentProduct.accountsOnFile) {
                    for (var _b = 0, _c = paymentProduct.accountsOnFile; _b < _c.length; _b++) {
                        var aof = _c[_b];
                        _accountsOnFile.push(aof);
                        _accountOnFileById[aof.id] = aof;
                        _paymentProductByAccountOnFileId[aof.id] = paymentProduct;
                    }
                }
            }
        }
    }
    var BasicPaymentProducts = /** @class */ (function () {
        function BasicPaymentProducts(json) {
            this.json = json;
            this.basicPaymentProducts = [];
            this.basicPaymentProductById = {};
            this.basicPaymentProductByAccountOnFileId = {};
            this.accountsOnFile = [];
            this.accountOnFileById = {};
            _parseJson(json, this.basicPaymentProducts, this.accountsOnFile, this.basicPaymentProductById, this.accountOnFileById, this.basicPaymentProductByAccountOnFileId);
        }
        return BasicPaymentProducts;
    }());
    return BasicPaymentProducts;
});
///<amd-module name="connectsdk.BasicPaymentItems"/>
define("connectsdk.BasicPaymentItems", ["require", "exports"], function (require, exports) {
    "use strict";
    function _parseJson(_products, _groups, _basicPaymentItems) {
        if (_groups) {
            var groupReplacements = {};
            for (var _i = 0, _a = _products.basicPaymentProducts; _i < _a.length; _i++) {
                var product = _a[_i];
                // becomes true if the product has been matched with a group.
                var groupMatch = false;
                for (var _b = 0, _c = _groups.basicPaymentProductGroups; _b < _c.length; _b++) {
                    var group = _c[_b];
                    if (product.paymentProductGroup === group.id) {
                        // Product has been matched to a group
                        groupMatch = true;
                        if (!groupReplacements[group.id]) {
                            // Group has not been added as replacement yet
                            _basicPaymentItems.basicPaymentItems.push(group.copy());
                            groupReplacements[group.id] = true;
                        }
                        // Products can not match with more then one group
                        break;
                    }
                }
                if (!groupMatch) {
                    _basicPaymentItems.basicPaymentItems.push(product.copy());
                }
            }
        }
        else {
            for (var _d = 0, _e = _products.basicPaymentProducts; _d < _e.length; _d++) {
                var product = _e[_d];
                _basicPaymentItems.basicPaymentItems.push(product.copy());
            }
        }
        for (var _f = 0, _g = _basicPaymentItems.basicPaymentItems; _f < _g.length; _f++) {
            var basicPaymentItem = _g[_f];
            _basicPaymentItems.basicPaymentItemById[basicPaymentItem.id] = basicPaymentItem;
            if (basicPaymentItem.accountsOnFile) {
                for (var _h = 0, _j = basicPaymentItem.accountsOnFile; _h < _j.length; _h++) {
                    var aof = _j[_h];
                    _basicPaymentItems.accountsOnFile.push(aof);
                    _basicPaymentItems.accountOnFileById[aof.id] = aof;
                }
            }
        }
    }
    var BasicPaymentItems = /** @class */ (function () {
        function BasicPaymentItems(products, groups) {
            this.basicPaymentItems = [];
            this.basicPaymentItemById = {};
            this.accountsOnFile = [];
            this.accountOnFileById = {};
            _parseJson(products, groups, this);
        }
        return BasicPaymentItems;
    }());
    return BasicPaymentItems;
});
///<amd-module name="connectsdk.C2SCommunicatorConfiguration"/>
define("connectsdk.C2SCommunicatorConfiguration", ["require", "exports"], function (require, exports) {
    "use strict";
    var C2SCommunicatorConfiguration = /** @class */ (function () {
        function C2SCommunicatorConfiguration(sessionDetails, apiVersion) {
            this.clientSessionId = sessionDetails.clientSessionId;
            this.customerId = sessionDetails.customerId;
            this.clientApiUrl = sessionDetails.clientApiUrl;
            this.assetUrl = sessionDetails.assetUrl;
            if (!this.clientApiUrl) {
                throw new Error("This version of the connectSDK requires an clientApiUrl, which you did not provide.");
            }
            if (!this.assetUrl) {
                throw new Error("This version of the connectSDK requires an assetUrl, which you did not provide.");
            }
            // now that the clientApiUrl is set check when if the api version is set in the URL, its the correct version break if not.
            if (this.clientApiUrl.indexOf("//") === -1) {
                throw new Error("A valid URL is required for the clientApiUrl, you provided '" + this.clientApiUrl + "'");
            }
            var tester = this.clientApiUrl.split("/"); // [0] = (http(s): || "") , [1] = "", [2] = "host:port", [3+] = path
            if (tester[0] !== "" && tester[0].indexOf("http") !== 0) {
                throw new Error("A valid URL is required for the clientApiUrl, you provided '" + this.clientApiUrl + "'");
            }
            // if you cannot provide an URL that starts with (http(s)::)// and want an error: please provide a PR :)
            var path = tester.splice(3).join("/"); // the path (if no path; path = "").
            if (!path) {
                this.clientApiUrl += "/" + apiVersion;
            }
            else if (path === "client") {
                this.clientApiUrl += "/" + apiVersion.split("/")[1];
            }
            else if (path.indexOf(apiVersion) !== 0 || path.length !== apiVersion.length) {
                throw new Error("This version of the connectSDK is only compatible with " + apiVersion + ", you supplied: '" + path + "'");
            }
        }
        return C2SCommunicatorConfiguration;
    }());
    return C2SCommunicatorConfiguration;
});
///<amd-module name="connectsdk.C2SPaymentProductContext"/>
define("connectsdk.C2SPaymentProductContext", ["require", "exports"], function (require, exports) {
    "use strict";
    var C2SPaymentProductContext = /** @class */ (function () {
        function C2SPaymentProductContext(payload) {
            this.totalAmount = payload.totalAmount;
            this.countryCode = payload.countryCode;
            this.isRecurring = payload.isRecurring;
            this.currency = payload.currency;
            if (typeof payload.locale !== "undefined") {
                this.locale = payload.locale;
            }
            this.isInstallments = payload.isInstallments || false;
            if (typeof payload.accountOnFileId !== "undefined") {
                this.accountOnFileId = payload.accountOnFileId;
            }
            if (typeof payload.environment !== "undefined") {
                this.environment = payload.environment;
            }
        }
        return C2SPaymentProductContext;
    }());
    return C2SPaymentProductContext;
});
///<amd-module name="connectsdk.GooglePay"/>
define("connectsdk.GooglePay", ["require", "exports", "connectsdk.promise", "connectsdk.Util"], function (require, exports, Promise, Util) {
    "use strict";
    var util = Util.getInstance();
    var GooglePay = /** @class */ (function () {
        function GooglePay() {
            var _paymentProductSpecificInputs = {};
            var _context;
            var _gateway = "";
            var _networks = [];
            var _paymentsClient;
            // Only base is needed to trigger isReadyToPay
            function _getBaseCardPaymentMethod() {
                return {
                    type: "CARD",
                    parameters: {
                        allowedAuthMethods: ["PAN_ONLY", "CRYPTOGRAM_3DS"],
                        allowedCardNetworks: _networks
                    }
                };
            }
            function _getTokenizationSpecification() {
                return {
                    type: "PAYMENT_GATEWAY",
                    parameters: {
                        gateway: _gateway,
                        gatewayMerchantId: _paymentProductSpecificInputs.googlePay.gatewayMerchantId
                    }
                };
            }
            // To prefetch payment data we need base + tokenizationSpecification
            function _getCardPaymentMethod() {
                return Object.assign({}, _getBaseCardPaymentMethod(), {
                    tokenizationSpecification: _getTokenizationSpecification()
                });
            }
            function _getTransactionInfo() {
                return {
                    totalPriceStatus: "NOT_CURRENTLY_KNOWN",
                    currencyCode: _context === null || _context === void 0 ? void 0 : _context.currency
                };
                // Note that the cast is necessary, because the TypeScript definition makes totalPrice required even though it isn't
            }
            function _getMerchantInfo() {
                return {
                    merchantName: _paymentProductSpecificInputs.googlePay.merchantName
                };
                // Note that the cast is necessary, because the TypeScript definition makes merchantId required even though it isn't
            }
            function _getGooglePaymentDataRequest() {
                return {
                    apiVersion: 2,
                    apiVersionMinor: 0,
                    allowedPaymentMethods: [_getBaseCardPaymentMethod()]
                };
            }
            function _getGooglePaymentDataRequestForPrefetch() {
                // transactionInfo must be set but does not affect cache
                return {
                    apiVersion: 2,
                    apiVersionMinor: 0,
                    allowedPaymentMethods: [_getCardPaymentMethod()],
                    transactionInfo: _getTransactionInfo(),
                    merchantInfo: _getMerchantInfo()
                };
            }
            function _getGooglePaymentsClient() {
                if (typeof _paymentsClient === "undefined") {
                    var googlePayEnvironment = "TEST";
                    if (_context.environment === "PROD") {
                        googlePayEnvironment = "PRODUCTION";
                    }
                    if (window.google) {
                        _paymentsClient = new google.payments.api.PaymentsClient({ environment: googlePayEnvironment });
                    }
                    else {
                        console.error("The Google Pay API script was not loaded https://developers.google.com/pay/api/web/guides/tutorial#js-load");
                    }
                }
                return _paymentsClient;
            }
            /**
             * Prefetch payment data to improve performance
             *
             * @see {@link https://developers.google.com/pay/api/web/reference/client#prefetchPaymentData|prefetchPaymentData()}
             */
            function _prefetchGooglePaymentData() {
                var paymentDataRequest = _getGooglePaymentDataRequestForPrefetch();
                var paymentsClient = _getGooglePaymentsClient();
                var googlePaySpecificInputs = _paymentProductSpecificInputs.googlePay;
                // Prefetching is only effective when all information is provided
                if (googlePaySpecificInputs.gatewayMerchantId && googlePaySpecificInputs.merchantName) {
                    paymentsClient.prefetchPaymentData(paymentDataRequest);
                }
                else {
                    console.warn("Prefetching payment data was not triggered because of missing information. " +
                        "gatewayMerchantId: " +
                        googlePaySpecificInputs.gatewayMerchantId +
                        ", merchantName: " +
                        googlePaySpecificInputs.merchantName);
                }
            }
            this.isGooglePayAvailable = function (context, paymentProductSpecificInputs, googlePayData) {
                _context = context;
                _paymentProductSpecificInputs = paymentProductSpecificInputs;
                _gateway = googlePayData.gateway;
                _networks = googlePayData.networks;
                if (_networks && _networks.length > 0) {
                    var paymentsClient = _getGooglePaymentsClient();
                    if (!paymentsClient) {
                        util.paymentProductsThatAreNotSupportedInThisBrowser.push(util.googlePayPaymentProductId);
                        return Promise.reject("The Google Pay API script was not loaded https://developers.google.com/pay/api/web/guides/tutorial#js-load");
                    }
                    else {
                        var promise_1 = new Promise();
                        paymentsClient
                            .isReadyToPay(_getGooglePaymentDataRequest())
                            .then(function (response) {
                            promise_1.resolve(response.result);
                            _prefetchGooglePaymentData();
                        })["catch"](function (e) {
                            util.paymentProductsThatAreNotSupportedInThisBrowser.push(util.googlePayPaymentProductId);
                            promise_1.reject(e);
                        });
                        return promise_1;
                    }
                }
                else {
                    util.paymentProductsThatAreNotSupportedInThisBrowser.push(util.googlePayPaymentProductId);
                    return Promise.reject("There are no product networks available");
                }
            };
            this.isMerchantIdProvided = function (paymentProductSpecificInputs) {
                if (paymentProductSpecificInputs && paymentProductSpecificInputs.googlePay && paymentProductSpecificInputs.googlePay.merchantId) {
                    return !!paymentProductSpecificInputs.googlePay.merchantId;
                }
                else {
                    util.paymentProductsThatAreNotSupportedInThisBrowser.push(util.googlePayPaymentProductId);
                    return false;
                }
            };
        }
        return GooglePay;
    }());
    return GooglePay;
});
///<amd-module name="connectsdk.IinDetailsResponse"/>
define("connectsdk.IinDetailsResponse", ["require", "exports"], function (require, exports) {
    "use strict";
    var IinDetailsResponse = /** @class */ (function () {
        function IinDetailsResponse(status, json) {
            this.status = status;
            this.json = json;
            if (json) {
                // If the JSON is actually an ErrorResponseJSON, these properties don't exist and the fields will remain undefined
                json = json;
                this.countryCode = json.countryCode;
                this.paymentProductId = json.paymentProductId;
                this.isAllowedInContext = json.isAllowedInContext;
                this.coBrands = json.coBrands;
            }
        }
        return IinDetailsResponse;
    }());
    return IinDetailsResponse;
});
///<amd-module name="connectsdk.net"/>
define("connectsdk.net", ["require", "exports", "connectsdk.promise"], function (require, exports, Promise) {
    "use strict";
    /**
     * Removes leading and trailing whitespace.
     */
    var trim = typeof "".trim === "function" ? function (s) { return s.trim(); } : function (s) { return s.replace(/^\s\s*/, "").replace(/\s\s*$/, ""); };
    var parseXML = window.DOMParser
        ? function (text) { return new DOMParser().parseFromString(text, "text/xml"); }
        : function (text) {
            var xml = new ActiveXObject("Microsoft.XMLDOM");
            xml.async = "false";
            xml.loadXML(text);
            return xml;
        };
    var JSONPCallback = /** @class */ (function () {
        function JSONPCallback(url, success, failure) {
            var _this = this;
            this.url = url;
            this.success = success;
            this.failure = failure;
            this.methodName = "__connectsdk_jsonp_" + new Date().getTime();
            var runCallback = function (json) {
                _this.success(json);
                _this.teardown();
            };
            window[this.methodName] = runCallback;
        }
        JSONPCallback.prototype.run = function () {
            var _this = this;
            this.scriptTag = document.createElement("script");
            this.scriptTag.id = this.methodName;
            this.scriptTag.src = this.url.replace("{callback}", this.methodName);
            this.scriptTag.onerror = function () { return _this.failure(); };
            document.body.appendChild(this.scriptTag);
        };
        JSONPCallback.prototype.teardown = function () {
            window[this.methodName] = null;
            try {
                delete window[this.methodName];
            }
            catch (e) {
                /* ignore */
            }
            if (this.scriptTag) {
                document.body.removeChild(this.scriptTag);
            }
        };
        return JSONPCallback;
    }());
    function xhr() {
        if (typeof XMLHttpRequest !== "undefined" && (window.location.protocol !== "file:" || !window.ActiveXObject)) {
            return new XMLHttpRequest();
        }
        else {
            try {
                return new ActiveXObject("Msxml2.XMLHTTP.6.0");
            }
            catch (e) {
                /* ignore */
            }
            try {
                return new ActiveXObject("Msxml2.XMLHTTP.3.0");
            }
            catch (e) {
                /* ignore */
            }
            try {
                return new ActiveXObject("Msxml2.XMLHTTP");
            }
            catch (e) {
                /* ignore */
            }
        }
        throw new Error("Could not initialze xhr");
    }
    function successfulRequest(request) {
        return (request.status >= 200 && request.status < 300) || request.status == 304 || (request.status == 0 && !!request.responseText);
    }
    var Net = /** @class */ (function () {
        function Net() {
        }
        /**
         * Serialize JavaScript for HTTP requests.
         *
         * @param {Object} object An Array or Object
         * @returns {String} A string suitable for a GET or POST request
         */
        Net.serialize = function (object) {
            if (!object) {
                return "";
            }
            if (typeof object === "string") {
                return object;
            }
            var results = [];
            for (var key in object) {
                results.push(encodeURIComponent(key) + "=" + encodeURIComponent(object[key]));
            }
            return results.join("&");
        };
        /**
         * Parses JSON represented as a string.
         *
         * @param {String} string The original string
         * @returns {Object} A JavaScript object
         */
        Net.parseJSON = function (string) {
            if (typeof string !== "string" || !string) {
                return null;
            }
            string = trim(string);
            return window.JSON.parse(string);
        };
        /**
         * Parses XML represented as a string.
         *
         * @param {String} string The original string
         * @returns {Object} A JavaScript object
         */
        Net.parseXML = function (text) {
            return parseXML(text);
        };
        /**
         * Creates an Ajax request.  Returns an object that can be used
         * to chain calls.  For example:
         *
         *      $t.post("/post-test")
         *        .data({ key: "value" })
         *        .end(function(res) {
         *          assert.equal("value", res.responseText);
         *        });
         *
         *      $t.get("/get-test")
         *        .set("Accept", "text/html")
         *        .end(function(res) {
         *          assert.equal("Sample text", res.responseText);
         *        });
         *
         * The available chained methods are:
         *
         * `set` -- set a HTTP header
         * `data` -- the postBody
         * `end` -- send the request over the network, and calls your callback with a `res` object
         * `send` -- sends the request and calls `data`: `.send({ data: value }, function(res) { });`
         *
         * @param {String} The URL to call
         * @param {Object} Optional settings
         * @returns {Object} A chainable object for further configuration
         */
        Net.ajax = function (url, options) {
            var request = xhr();
            var promise = new Promise();
            var opts = typeof options !== "undefined" ? options : {};
            function respondToReadyState() {
                if (request.readyState == 4) {
                    var contentType = request["mimeType"] || request.getResponseHeader("content-type") || "";
                    var response = {
                        status: request.status,
                        responseText: request.responseText,
                        success: successfulRequest(request)
                    };
                    if (/json/.test(contentType)) {
                        response.responseJSON = Net.parseJSON(request.responseText);
                    }
                    else if (/xml/.test(contentType)) {
                        response.responseXML = Net.parseXML(request.responseText);
                    }
                    if (opts.callback) {
                        return opts.callback(response, request);
                    }
                    if (response.success) {
                        if (opts.success) {
                            opts.success(response, request);
                        }
                        promise.resolve(response);
                    }
                    else {
                        if (opts.error) {
                            opts.error(response, request);
                        }
                        promise.reject(response);
                    }
                }
            }
            function setHeaders() {
                var defaults = {
                    Accept: "text/javascript, application/json, text/html, application/xml, text/xml, */*",
                    "Content-Type": "application/json"
                };
                opts.headers = opts.headers || {};
                /**
                 * Merge headers with defaults.
                 */
                for (var name_1 in defaults) {
                    // eslint-disable-next-line no-prototype-builtins
                    if (!opts.headers.hasOwnProperty(name_1)) {
                        opts.headers[name_1] = defaults[name_1];
                    }
                }
                for (var name_2 in opts.headers) {
                    request.setRequestHeader(name_2, opts.headers[name_2]);
                }
            }
            opts.method = opts.method ? opts.method.toLowerCase() : "get";
            opts.asynchronous = opts.asynchronous || true;
            opts.postBody = opts.postBody || "";
            request.onreadystatechange = respondToReadyState;
            request.open(opts.method, url, opts.asynchronous);
            opts.headers = opts.headers || {};
            if (opts.contentType) {
                opts.headers["Content-Type"] = opts.contentType;
            }
            if (typeof opts.postBody !== "string") {
                // Serialize JavaScript
                opts.postBody = Net.serialize(opts.postBody);
            }
            function send() {
                try {
                    setHeaders();
                    request.send(opts.postBody);
                }
                catch (e) {
                    if (opts.error) {
                        opts.error();
                    }
                }
            }
            var chain = {
                set: function (key, value) {
                    opts.headers = opts.headers || {};
                    opts.headers[key] = value;
                    return chain;
                },
                send: function (data, callback) {
                    opts.postBody = Net.serialize(data);
                    opts.callback = callback;
                    send();
                    return chain;
                },
                end: function (callback) {
                    opts.callback = callback;
                    send();
                    return chain;
                },
                data: function (data) {
                    opts.postBody = Net.serialize(data);
                    return chain;
                },
                // eslint-disable-next-line @typescript-eslint/no-explicit-any
                then: function (success, failure) {
                    chain.end();
                    promise.then(success, failure);
                    return chain;
                }
            };
            return chain;
        };
        /**
         * An Ajax GET request.
         *
         *      $t.get("/get-test")
         *        .set("Accept", "text/html")
         *        .end(function(res) {
         *          assert.equal("Sample text", res.responseText);
         *        });
         *
         * @param {String} url The URL to request
         * @param {Object} options The Ajax request options
         * @returns {Object} A chainable object for further configuration
         */
        Net.get = function (url, options) {
            if (typeof options === "undefined") {
                options = {};
            }
            options.method = "get";
            return Net.ajax(url, options);
        };
        /**
         * An Ajax POST request.
         *
         *      $t.post("/post-test")
         *        .data({ key: "value" })
         *        .end(function(res) {
         *          assert.equal("value", res.responseText);
         *        });
         *
         * @param {String} url The URL to request
         * @param {Object} options The Ajax request options (`postBody` may come in handy here)
         * @returns {Object} An object for further chaining with promises
         */
        Net.post = function (url, options) {
            if (typeof options === "undefined") {
                options = {};
            }
            options.method = "post";
            return Net.ajax(url, options);
        };
        /**
         * A jsonp request.  Example:
         *
         *     var url = "http://feeds.delicious.com/v1/json/";
         *     url += "alex_young/javascript?callback={callback}";
         *
         *     connectsdk.net.jsonp(url, {
         *       success: function(json) {
         *         console.log(json);
         *       }
         *     });
         *
         * @param {String} url The URL to request
         */
        Net.jsonp = function (url, options) {
            var failure = typeof options.failure !== "undefined"
                ? options.failure
                : function () {
                    /* ignore */
                };
            var callback = new JSONPCallback(url, options.success, failure);
            callback.run();
        };
        return Net;
    }());
    return Net;
});
///<amd-module name="connectsdk.PublicKeyResponse"/>
define("connectsdk.PublicKeyResponse", ["require", "exports"], function (require, exports) {
    "use strict";
    var PublicKeyResponse = /** @class */ (function () {
        function PublicKeyResponse(json) {
            this.json = json;
            this.keyId = json.keyId;
            this.publicKey = json.publicKey;
        }
        return PublicKeyResponse;
    }());
    return PublicKeyResponse;
});
///<amd-module name="connectsdk.C2SCommunicator"/>
define("connectsdk.C2SCommunicator", ["require", "exports", "connectsdk.ApplePay", "connectsdk.GooglePay", "connectsdk.IinDetailsResponse", "connectsdk.net", "connectsdk.promise", "connectsdk.PublicKeyResponse", "connectsdk.Util"], function (require, exports, ApplePay, GooglePay, IinDetailsResponse, Net, Promise, PublicKeyResponse, Util) {
    "use strict";
    var util = Util.getInstance();
    var C2SCommunicator = /** @class */ (function () {
        function C2SCommunicator(c2SCommunicatorConfiguration, paymentProduct) {
            var _this = this;
            var _c2SCommunicatorConfiguration = c2SCommunicatorConfiguration;
            var _cache = {};
            var _providedPaymentProduct = paymentProduct;
            var _GooglePay = new GooglePay();
            var _ApplePay = new ApplePay();
            var _mapType = {
                expirydate: "tel",
                string: "text",
                numericstring: "tel",
                integer: "number",
                expirationDate: "tel"
            };
            function _startsWith(string, prefix) {
                return string.indexOf(prefix) === 0;
            }
            function _endsWith(string, suffix) {
                return string.indexOf(suffix, string.length - suffix.length) !== -1;
            }
            function _formatUrl(url) {
                return url && _endsWith(url, "/") ? url : url + "/";
            }
            function _formatImageUrl(url, imageUrl) {
                url = _formatUrl(url);
                // _cleanJSON can be called multiple times with the same data (which is cached between calls).
                // Don't prepend the url after the first time.
                if (_startsWith(imageUrl, url)) {
                    return imageUrl;
                }
                return url + imageUrl;
            }
            function _constructUrl(path) {
                return _formatUrl(_c2SCommunicatorConfiguration.clientApiUrl) + _c2SCommunicatorConfiguration.customerId + path;
            }
            function _constructUrlFromContext(path, context, includeLocale) {
                if (includeLocale === void 0) { includeLocale = true; }
                var urlParameterLocale = includeLocale && context.locale ? "&locale=" + context.locale : "";
                return _constructUrl(path +
                    "?countryCode=" +
                    context.countryCode +
                    "&isRecurring=" +
                    context.isRecurring +
                    "&amount=" +
                    context.totalAmount +
                    "&currencyCode=" +
                    context.currency +
                    urlParameterLocale);
            }
            function _constructCacheKeyFromContext(prefix, context, includeLocale) {
                if (includeLocale === void 0) { includeLocale = true; }
                var cacheKeyLocale = includeLocale && context.locale ? context.locale + "_" : "";
                return prefix + context.totalAmount + "_" + context.countryCode + "_" + cacheKeyLocale + context.isRecurring + "_" + context.currency;
            }
            function _constructCacheKeyFromKeyValues(prefix, values) {
                var cacheKey = prefix;
                for (var key in values) {
                    // eslint-disable-next-line no-prototype-builtins
                    if (values.hasOwnProperty(key)) {
                        cacheKey += "_" + values[key].key + "_" + values[key].value;
                    }
                }
                return cacheKey;
            }
            function _cleanJSON(json, url) {
                for (var _i = 0, _a = json.fields; _i < _a.length; _i++) {
                    var field = _a[_i];
                    field.type = field.displayHints && field.displayHints.obfuscate ? "password" : _mapType[field.type];
                    // helper code for templating tools like Handlebars
                    for (var validatorKey in field.dataRestrictions.validators) {
                        field.validators = field.validators || [];
                        field.validators.push(validatorKey);
                    }
                    if (field.displayHints && field.displayHints.formElement && field.displayHints.formElement.type === "list") {
                        field.displayHints.formElement.list = true;
                    }
                    // full image paths
                    if (field.displayHints && field.displayHints.tooltip && field.displayHints.tooltip.image) {
                        field.displayHints.tooltip.image = _formatImageUrl(url, field.displayHints.tooltip.image);
                    }
                }
                // The server orders in a different way, so we apply the sortorder
                json.fields.sort(function (a, b) {
                    var _a, _b, _c, _d;
                    var aDisplayOrder = (_b = (_a = a.displayHints) === null || _a === void 0 ? void 0 : _a.displayOrder) !== null && _b !== void 0 ? _b : 0;
                    var bDisplayOrder = (_d = (_c = b.displayHints) === null || _c === void 0 ? void 0 : _c.displayOrder) !== null && _d !== void 0 ? _d : 0;
                    if (aDisplayOrder < bDisplayOrder) {
                        return -1;
                    }
                    return 1;
                });
                // set full image path
                json.displayHints.logo = _formatImageUrl(url, json.displayHints.logo);
                if (json.accountsOnFile) {
                    for (var _b = 0, _c = json.accountsOnFile; _b < _c.length; _b++) {
                        var aof = _c[_b];
                        aof.displayHints.logo = _formatImageUrl(url, aof.displayHints.logo);
                    }
                }
                return json;
            }
            function _extendLogoUrl(json, url, postfix) {
                for (var _i = 0, _a = json["paymentProduct" + postfix]; _i < _a.length; _i++) {
                    var product = _a[_i];
                    product.displayHints.logo = _formatImageUrl(url, product.displayHints.logo);
                    if (product.accountsOnFile) {
                        for (var _b = 0, _c = product.accountsOnFile; _b < _c.length; _b++) {
                            var aof = _c[_b];
                            aof.displayHints.logo = _formatImageUrl(url, aof.displayHints.logo);
                        }
                    }
                }
                json["paymentProduct" + postfix].sort(function (a, b) {
                    if (a.displayHints.displayOrder < b.displayHints.displayOrder) {
                        return -1;
                    }
                    return 1;
                });
                return json;
            }
            function _isPaymentProductInList(list, paymentProductId) {
                for (var _i = 0, list_1 = list; _i < list_1.length; _i++) {
                    var product = list_1[_i];
                    if (product && product.id === paymentProductId) {
                        return true;
                    }
                }
                return false;
            }
            function _getGooglePayData(list, paymentProductId) {
                for (var _i = 0, list_2 = list; _i < list_2.length; _i++) {
                    var product = list_2[_i];
                    if (product && product.id === paymentProductId) {
                        return product.paymentProduct320SpecificData;
                    }
                }
                return undefined;
            }
            function _resolveGetBasicPaymentProducts(json, promise, cacheKey) {
                util.filterOutProductsThatAreNotSupportedInThisBrowser(json);
                _cache[cacheKey] = json;
                if (json.paymentProducts.length === 0) {
                    promise.reject("No payment products available");
                }
                else {
                    promise.resolve(json);
                }
            }
            this.getBasicPaymentProducts = function (context, paymentProductSpecificInputs) {
                paymentProductSpecificInputs = paymentProductSpecificInputs || {};
                var cacheBust = new Date().getTime();
                var cacheKey = _constructCacheKeyFromContext("getPaymentProducts-", context) + "_" + JSON.stringify(paymentProductSpecificInputs);
                if (_cache[cacheKey]) {
                    return Promise.resolve(_cache[cacheKey]);
                }
                else {
                    var promise_2 = new Promise();
                    var url = _constructUrlFromContext("/products", context) + "&hide=fields&cacheBust=" + cacheBust;
                    var metadata = util.getMetadata();
                    Net.get(url)
                        .set("X-GCS-ClientMetaInfo", util.base64Encode(metadata))
                        .set("Authorization", "GCS v1Client:" + _c2SCommunicatorConfiguration.clientSessionId)
                        .end(function (res) {
                        if (res.success) {
                            var json_1 = _extendLogoUrl(res.responseJSON, _c2SCommunicatorConfiguration.assetUrl, "s");
                            if (_isPaymentProductInList(json_1.paymentProducts, util.applePayPaymentProductId)) {
                                _ApplePay.isApplePayAvailable();
                            }
                            if (_isPaymentProductInList(json_1.paymentProducts, util.googlePayPaymentProductId) && _GooglePay.isMerchantIdProvided(paymentProductSpecificInputs)) {
                                var googlePayData = _getGooglePayData(json_1.paymentProducts, util.googlePayPaymentProductId);
                                _GooglePay.isGooglePayAvailable(context, paymentProductSpecificInputs, googlePayData).then(function () { return _resolveGetBasicPaymentProducts(json_1, promise_2, cacheKey); }, function () { return _resolveGetBasicPaymentProducts(json_1, promise_2, cacheKey); });
                            }
                            else {
                                _resolveGetBasicPaymentProducts(json_1, promise_2, cacheKey);
                            }
                        }
                        else {
                            promise_2.reject(res.responseJSON);
                        }
                    });
                    return promise_2;
                }
            };
            this.getBasicPaymentProductGroups = function (context) {
                var cacheBust = new Date().getTime();
                var cacheKey = _constructCacheKeyFromContext("getPaymentProductGroups-", context);
                if (_cache[cacheKey]) {
                    return Promise.resolve(_cache[cacheKey]);
                }
                else {
                    var promise_3 = new Promise();
                    var url = _constructUrlFromContext("/productgroups", context) + "&hide=fields&cacheBust=" + cacheBust;
                    var metadata = util.getMetadata();
                    Net.get(url)
                        .set("X-GCS-ClientMetaInfo", util.base64Encode(metadata))
                        .set("Authorization", "GCS v1Client:" + _c2SCommunicatorConfiguration.clientSessionId)
                        .end(function (res) {
                        if (res.success) {
                            var json = _extendLogoUrl(res.responseJSON, _c2SCommunicatorConfiguration.assetUrl, "Groups");
                            _cache[cacheKey] = json;
                            promise_3.resolve(json);
                        }
                        else {
                            promise_3.reject(res.responseJSON);
                        }
                    });
                    return promise_3;
                }
            };
            this.getPaymentProduct = function (paymentProductId, context, paymentProductSpecificInputs) {
                paymentProductSpecificInputs = paymentProductSpecificInputs || {};
                var cacheBust = new Date().getTime();
                var cacheKey = _constructCacheKeyFromContext("getPaymentProduct-" + paymentProductId, context) + "_" + JSON.stringify(paymentProductSpecificInputs);
                if (util.paymentProductsThatAreNotSupportedInThisBrowser.indexOf(paymentProductId) > -1) {
                    return Promise.reject({
                        errorId: "48b78d2d-1b35-4f8b-92cb-57cc2638e901",
                        errors: [
                            {
                                code: "1007",
                                propertyName: "productId",
                                message: "UNKNOWN_PRODUCT_ID",
                                httpStatusCode: 404
                            },
                        ]
                    });
                }
                else {
                    if (_providedPaymentProduct && _providedPaymentProduct.id === paymentProductId) {
                        if (!_cache[cacheKey]) {
                            _cache[cacheKey] = _cleanJSON(_providedPaymentProduct, _c2SCommunicatorConfiguration.assetUrl);
                        }
                        return Promise.resolve(_cache[cacheKey]);
                    }
                    else if (_cache[cacheKey]) {
                        return Promise.resolve(_cache[cacheKey]);
                    }
                    else {
                        var promise_4 = new Promise();
                        var url = _constructUrlFromContext("/products/" + paymentProductId, context);
                        if (paymentProductId === util.bancontactPaymentProductId &&
                            paymentProductSpecificInputs &&
                            paymentProductSpecificInputs.bancontact &&
                            paymentProductSpecificInputs.bancontact.forceBasicFlow) {
                            // Add query parameter to products call to force basic flow for bancontact
                            url += "&forceBasicFlow=" + paymentProductSpecificInputs.bancontact.forceBasicFlow;
                        }
                        url += "&cacheBust=" + cacheBust;
                        var metadata = util.getMetadata();
                        Net.get(url)
                            .set("X-GCS-ClientMetaInfo", util.base64Encode(metadata))
                            .set("Authorization", "GCS v1Client:" + _c2SCommunicatorConfiguration.clientSessionId)
                            .end(function (res) {
                            if (res.success) {
                                var cleanedJSON_1 = _cleanJSON(res.responseJSON, _c2SCommunicatorConfiguration.assetUrl);
                                _cache[cacheKey] = cleanedJSON_1;
                                if (paymentProductId === util.applePayPaymentProductId && !_ApplePay.isApplePayAvailable()) {
                                    // Apple Pay is available in the payment context but the client does not support it.
                                    promise_4.reject({
                                        message: "Apple Pay is not available in the client",
                                        json: cleanedJSON_1
                                    });
                                }
                                else if (paymentProductId === util.googlePayPaymentProductId && _GooglePay.isMerchantIdProvided(paymentProductSpecificInputs)) {
                                    var googlePayData = cleanedJSON_1.paymentProduct320SpecificData;
                                    _GooglePay.isGooglePayAvailable(context, paymentProductSpecificInputs, googlePayData).then(function (isGooglePayAvailable) {
                                        if (isGooglePayAvailable) {
                                            promise_4.resolve(cleanedJSON_1);
                                        }
                                        else {
                                            // isGooglePayAvailable returned false so Google Pay is not available, so reject getPaymentProduct
                                            promise_4.reject({
                                                message: "Google Pay is not available in the client",
                                                json: cleanedJSON_1
                                            });
                                        }
                                    }, function (reason) {
                                        // isGooglePayAvailable rejected so not available
                                        promise_4.reject({
                                            reason: reason,
                                            json: cleanedJSON_1
                                        });
                                    });
                                }
                                else {
                                    promise_4.resolve(cleanedJSON_1);
                                }
                            }
                            else {
                                promise_4.reject(res.responseJSON);
                            }
                        });
                        return promise_4;
                    }
                }
            };
            this.getPaymentProductGroup = function (paymentProductGroupId, context) {
                var cacheBust = new Date().getTime();
                var cacheKey = _constructUrlFromContext("getPaymentProductGroup-" + paymentProductGroupId, context);
                if (_providedPaymentProduct && _providedPaymentProduct.id === paymentProductGroupId) {
                    if (!_cache[cacheKey]) {
                        _cache[cacheKey] = _cleanJSON(_providedPaymentProduct, _c2SCommunicatorConfiguration.assetUrl);
                    }
                    return Promise.resolve(_cache[cacheKey]);
                }
                else if (_cache[cacheKey]) {
                    return Promise.resolve(_cache[cacheKey]);
                }
                else {
                    var promise_5 = new Promise();
                    var url = _constructUrlFromContext("/productgroups/" + paymentProductGroupId, context) + "&cacheBust=" + cacheBust;
                    var metadata = util.getMetadata();
                    Net.get(url)
                        .set("X-GCS-ClientMetaInfo", util.base64Encode(metadata))
                        .set("Authorization", "GCS v1Client:" + _c2SCommunicatorConfiguration.clientSessionId)
                        .end(function (res) {
                        if (res.success) {
                            var cleanedJSON = _cleanJSON(res.responseJSON, _c2SCommunicatorConfiguration.assetUrl);
                            _cache[cacheKey] = cleanedJSON;
                            promise_5.resolve(cleanedJSON);
                        }
                        else {
                            promise_5.reject(res.responseJSON);
                        }
                    });
                    return promise_5;
                }
            };
            this.getPaymentProductIdByCreditCardNumber = function (partialCreditCardNumber, context) {
                var cacheKey = "getPaymentProductIdByCreditCardNumber-" + partialCreditCardNumber;
                if (_cache[cacheKey]) {
                    // cache is based on digit 1-6
                    return Promise.resolve(_cache[cacheKey]);
                }
                else {
                    var isEnoughDigits = partialCreditCardNumber.length >= 6;
                    if (isEnoughDigits) {
                        var promise_6 = new Promise();
                        var url = _constructUrl("/services/getIINdetails");
                        var metadata = util.getMetadata();
                        Net.post(url)
                            .data(JSON.stringify(_this.convertContextToIinDetailsContext(partialCreditCardNumber, context)))
                            .set("X-GCS-ClientMetaInfo", util.base64Encode(metadata))
                            .set("Authorization", "GCS v1Client:" + _c2SCommunicatorConfiguration.clientSessionId)
                            .end(function (res) {
                            if (res.success) {
                                var json_2 = res.responseJSON;
                                // check if this card is supported
                                // if isAllowedInContext is available in the response set status and resolve
                                // eslint-disable-next-line no-prototype-builtins
                                if (json_2.hasOwnProperty("isAllowedInContext")) {
                                    var status_3 = json_2.isAllowedInContext !== false ? "SUPPORTED" : "EXISTING_BUT_NOT_ALLOWED";
                                    var iinDetailsResponse = new IinDetailsResponse(status_3, json_2);
                                    _cache[cacheKey] = iinDetailsResponse;
                                    promise_6.resolve(iinDetailsResponse);
                                }
                                else {
                                    //if isAllowedInContext is not available get the payment product again to determine status and resolve
                                    _this.getPaymentProduct(json_2.paymentProductId, context).then(function (paymentProduct) {
                                        var status = paymentProduct ? "SUPPORTED" : "UNSUPPORTED";
                                        var iinDetailsResponse = new IinDetailsResponse(status, json_2);
                                        _cache[cacheKey] = iinDetailsResponse;
                                        promise_6.resolve(iinDetailsResponse);
                                    }, function () {
                                        var iinDetailsResponse = new IinDetailsResponse("UNKNOWN", json_2);
                                        promise_6.reject(iinDetailsResponse);
                                    });
                                }
                            }
                            else {
                                var iinDetailsResponse = new IinDetailsResponse("UNKNOWN", res.responseJSON);
                                promise_6.reject(iinDetailsResponse);
                            }
                        });
                        return promise_6;
                    }
                    else {
                        var iinDetailsResponse = new IinDetailsResponse("NOT_ENOUGH_DIGITS");
                        return Promise.resolve(iinDetailsResponse);
                    }
                }
            };
            this.convertContextToIinDetailsContext = function (partialCreditCardNumber, context) {
                var payload = {
                    bin: partialCreditCardNumber,
                    paymentContext: {
                        countryCode: context.countryCode,
                        isRecurring: context.isRecurring,
                        isInstallments: context.isInstallments,
                        amountOfMoney: {
                            amount: context.totalAmount,
                            currencyCode: context.currency
                        }
                    }
                };
                // Account on file id is needed only in case when the merchant
                // uses multiple payment platforms at the same time.
                if (typeof context.accountOnFileId !== "undefined") {
                    payload["accountOnFileId"] = context.accountOnFileId;
                }
                return payload;
            };
            this.getPublicKey = function () {
                var cacheKey = "publicKey";
                if (_cache[cacheKey]) {
                    return Promise.resolve(_cache[cacheKey]);
                }
                else {
                    var promise_7 = new Promise();
                    var url = _constructUrl("/crypto/publickey");
                    var metadata = util.getMetadata();
                    Net.get(url)
                        .set("X-GCS-ClientMetaInfo", util.base64Encode(metadata))
                        .set("Authorization", "GCS v1Client:" + _c2SCommunicatorConfiguration.clientSessionId)
                        .end(function (res) {
                        if (res.success) {
                            var publicKeyResponse = new PublicKeyResponse(res.responseJSON);
                            _cache[cacheKey] = publicKeyResponse;
                            promise_7.resolve(publicKeyResponse);
                        }
                        else {
                            promise_7.reject(res.responseJSON);
                        }
                    });
                    return promise_7;
                }
            };
            this.getPaymentProductNetworks = function (paymentProductId, context) {
                var cacheKey = _constructCacheKeyFromContext("paymentProductNetworks-" + paymentProductId, context, false);
                if (_cache[cacheKey]) {
                    return Promise.resolve(_cache[cacheKey]);
                }
                else {
                    var promise_8 = new Promise();
                    var url = _constructUrlFromContext("/products/" + paymentProductId + "/networks", context, false);
                    var metadata = util.getMetadata();
                    Net.get(url)
                        .set("X-GCS-ClientMetaInfo", util.base64Encode(metadata))
                        .set("Authorization", "GCS v1Client:" + _c2SCommunicatorConfiguration.clientSessionId)
                        .end(function (res) {
                        if (res.success) {
                            _cache[cacheKey] = res.responseJSON;
                            promise_8.resolve(res.responseJSON);
                        }
                        else {
                            promise_8.reject(res.responseJSON);
                        }
                    });
                    return promise_8;
                }
            };
            this.getPaymentProductDirectory = function (paymentProductId, currencyCode, countryCode) {
                var cacheKey = "getPaymentProductDirectory-" + paymentProductId + "_" + currencyCode + "_" + countryCode;
                if (_cache[cacheKey]) {
                    return Promise.resolve(_cache[cacheKey]);
                }
                else {
                    var promise_9 = new Promise();
                    var url = _constructUrl("/directory?countryCode=" + countryCode + "&currencyCode=" + currencyCode);
                    var metadata = util.getMetadata();
                    Net.get(url)
                        .set("X-GCS-ClientMetaInfo", util.base64Encode(metadata))
                        .set("Authorization", "GCS v1Client:" + _c2SCommunicatorConfiguration.clientSessionId)
                        .end(function (res) {
                        if (res.success) {
                            _cache[cacheKey] = res.responseJSON;
                            promise_9.resolve(res.responseJSON);
                        }
                        else {
                            promise_9.reject(res.responseJSON);
                        }
                    });
                    return promise_9;
                }
            };
            this.convertAmount = function (amount, source, target) {
                var cacheKey = "convertAmount-" + amount + "_" + source + "_" + target;
                if (_cache[cacheKey]) {
                    return Promise.resolve(_cache[cacheKey]);
                }
                else {
                    var promise_10 = new Promise();
                    var url = _constructUrl("/services/convert/amount?source=" + source + "&target=" + target + "&amount=" + amount);
                    var metadata = util.getMetadata();
                    Net.get(url)
                        .set("X-GCS-ClientMetaInfo", util.base64Encode(metadata))
                        .set("Authorization", "GCS v1Client:" + _c2SCommunicatorConfiguration.clientSessionId)
                        .end(function (res) {
                        if (res.success) {
                            _cache[cacheKey] = res.responseJSON;
                            promise_10.resolve(res.responseJSON);
                        }
                        else {
                            promise_10.reject(res.responseJSON);
                        }
                    });
                    return promise_10;
                }
            };
            this.getThirdPartyPaymentStatus = function (paymentId) {
                var promise = new Promise();
                var url = _constructUrl("/payments/" + paymentId + "/thirdpartystatus");
                var metadata = util.getMetadata();
                Net.get(url)
                    .set("X-GCS-ClientMetaInfo", util.base64Encode(metadata))
                    .set("Authorization", "GCS v1Client:" + _c2SCommunicatorConfiguration.clientSessionId)
                    .end(function (res) {
                    if (res.success) {
                        promise.resolve(res.responseJSON);
                    }
                    else {
                        promise.reject(res.responseJSON);
                    }
                });
                return promise;
            };
            this.getCustomerDetails = function (paymentProductId, context) {
                var cacheKey = _constructCacheKeyFromKeyValues("getCustomerDetails_" + paymentProductId + "_" + context.countryCode, context.values);
                if (_cache[cacheKey]) {
                    return Promise.resolve(_cache[cacheKey]);
                }
                else {
                    var promise_11 = new Promise();
                    var url = _constructUrl("/products/" + paymentProductId + "/customerDetails");
                    var metadata = util.getMetadata();
                    Net.post(url)
                        .data(JSON.stringify(context))
                        .set("X-GCS-ClientMetaInfo", util.base64Encode(metadata))
                        .set("Authorization", "GCS v1Client:" + _c2SCommunicatorConfiguration.clientSessionId)
                        .end(function (res) {
                        if (res.success) {
                            _cache[cacheKey] = res.responseJSON;
                            promise_11.resolve(res.responseJSON);
                        }
                        else {
                            promise_11.reject(res.responseJSON);
                        }
                    });
                    return promise_11;
                }
            };
            this.createPaymentProductSession = function (paymentProductId, context) {
                var cacheKey = "createPaymentProductSession_" + paymentProductId + "_" + context.validationURL + "_" + context.domainName + "_" + context.displayName;
                if (_cache[cacheKey]) {
                    return Promise.resolve(_cache[cacheKey]);
                }
                else {
                    var promise_12 = new Promise();
                    var url = _constructUrl("/products/" + paymentProductId + "/sessions");
                    var requestParameters = {
                        paymentProductSession302SpecificInput: {
                            validationUrl: context.validationURL,
                            domainName: context.domainName,
                            displayName: context.displayName
                        }
                    };
                    var metadata = util.getMetadata();
                    Net.post(url)
                        .data(JSON.stringify(requestParameters))
                        .set("X-GCS-ClientMetaInfo", util.base64Encode(metadata))
                        .set("Authorization", "GCS v1Client:" + _c2SCommunicatorConfiguration.clientSessionId)
                        .end(function (res) {
                        if (res.success) {
                            _cache[cacheKey] = res.responseJSON;
                            promise_12.resolve(res.responseJSON);
                        }
                        else {
                            promise_12.reject(res.responseJSON);
                        }
                    });
                    return promise_12;
                }
            };
            this.initApplePayPayment = function (context, paymentProductSpecificInput, networks) {
                var payload = JSON.parse(JSON.stringify(context));
                payload.displayName = paymentProductSpecificInput.merchantName;
                if (paymentProductSpecificInput.acquirerCountry) {
                    payload.acquirerCountry = paymentProductSpecificInput.acquirerCountry;
                }
                payload.networks = networks;
                return _ApplePay.initPayment(payload, _this);
            };
            this.transformPaymentProductJSON = function (json) {
                return _cleanJSON(json, _c2SCommunicatorConfiguration.assetUrl);
            };
        }
        return C2SCommunicator;
    }());
    return C2SCommunicator;
});
///<amd-module name="connectsdk.ValidationRuleBoletoBancarioRequiredness"/>
define("connectsdk.ValidationRuleBoletoBancarioRequiredness", ["require", "exports"], function (require, exports) {
    "use strict";
    var ValidationRuleBoletoBancarioRequiredness = /** @class */ (function () {
        function ValidationRuleBoletoBancarioRequiredness(json) {
            this.json = json;
            this.type = json.type;
            this.errorMessageId = json.type;
            this.fiscalNumberLength = json.attributes.fiscalNumberLength;
        }
        ValidationRuleBoletoBancarioRequiredness.prototype.validate = function (value, fiscalNumberValue) {
            if (typeof fiscalNumberValue === "undefined") {
                fiscalNumberValue = "";
            }
            return (fiscalNumberValue.length === this.fiscalNumberLength && value.length > 0) || fiscalNumberValue.length !== this.fiscalNumberLength;
        };
        ValidationRuleBoletoBancarioRequiredness.prototype.validateValue = function (request, fieldId) {
            var fiscalNumber = request.getUnmaskedValue("fiscalNumber");
            var fiscalNumberLength = (fiscalNumber === null || fiscalNumber === void 0 ? void 0 : fiscalNumber.length) || 0;
            if (fiscalNumberLength !== this.fiscalNumberLength) {
                // The field is not required for Boleto; allow anything
                return true;
            }
            var value = request.getValue(fieldId);
            return !!value;
        };
        return ValidationRuleBoletoBancarioRequiredness;
    }());
    return ValidationRuleBoletoBancarioRequiredness;
});
///<amd-module name="connectsdk.ValidationRuleEmailAddress"/>
define("connectsdk.ValidationRuleEmailAddress", ["require", "exports"], function (require, exports) {
    "use strict";
    var ValidationRuleEmailAddress = /** @class */ (function () {
        function ValidationRuleEmailAddress(json) {
            this.json = json;
            this.type = json.type;
            this.errorMessageId = json.type;
        }
        ValidationRuleEmailAddress.prototype.validate = function (value) {
            // eslint-disable-next-line no-useless-escape
            var regexp = new RegExp(/^[^@\.]+(\.[^@\.]+)*@([^@\.]+\.)*[^@\.]+\.[^@\.][^@\.]+$/i);
            return regexp.test(value);
        };
        ValidationRuleEmailAddress.prototype.validateValue = function (request, fieldId) {
            var value = request.getUnmaskedValue(fieldId);
            return !!value && this.validate(value);
        };
        return ValidationRuleEmailAddress;
    }());
    return ValidationRuleEmailAddress;
});
///<amd-module name="connectsdk.ValidationRuleExpirationDate"/>
define("connectsdk.ValidationRuleExpirationDate", ["require", "exports"], function (require, exports) {
    "use strict";
    function validateDateFormat(value) {
        // value is mmYY or mmYYYY
        var pattern = /\d{4}|\d{6}$/g;
        return pattern.test(value);
    }
    var ValidationRuleExpirationDate = /** @class */ (function () {
        function ValidationRuleExpirationDate(json) {
            this.json = json;
            this.type = json.type;
            this.errorMessageId = json.type;
        }
        ValidationRuleExpirationDate.prototype.validate = function (value) {
            value = value.replace(/[^\d]/g, "");
            if (!validateDateFormat(value)) {
                return false;
            }
            var split;
            if (value.length === 4) {
                split = [value.substring(0, 2), "20" + value.substring(2, 4)];
            }
            else if (value.length === 6) {
                split = [value.substring(0, 2), value.substring(2, 6)];
            }
            else {
                return false;
            }
            // The month is zero-based, so subtract one.
            var expirationMonth = Number(split[0]) - 1;
            var expirationYear = Number(split[1]);
            var expirationDate = new Date(expirationYear, expirationMonth, 1);
            // Compare the input with the parsed date, to check if the date rolled over.
            if (expirationDate.getMonth() !== Number(expirationMonth) || expirationDate.getFullYear() !== Number(expirationYear)) {
                return false;
            }
            // For comparison, set the current year & month and the maximum allowed expiration date.
            var nowWithDay = new Date();
            var now = new Date(nowWithDay.getFullYear(), nowWithDay.getMonth(), 1);
            var maxExpirationDate = new Date(nowWithDay.getFullYear() + 25, 11, 1);
            // The card is still valid if it expires this month.
            return expirationDate >= now && expirationDate <= maxExpirationDate;
        };
        ValidationRuleExpirationDate.prototype.validateValue = function (request, fieldId) {
            var value = request.getUnmaskedValue(fieldId);
            return !!value && this.validate(value);
        };
        return ValidationRuleExpirationDate;
    }());
    return ValidationRuleExpirationDate;
});
///<amd-module name="connectsdk.ValidationRuleFixedList"/>
define("connectsdk.ValidationRuleFixedList", ["require", "exports"], function (require, exports) {
    "use strict";
    var ValidationRuleFixedList = /** @class */ (function () {
        function ValidationRuleFixedList(json) {
            this.json = json;
            this.type = json.type;
            this.errorMessageId = json.type;
            this.allowedValues = json.attributes.allowedValues;
        }
        ValidationRuleFixedList.prototype.validate = function (value) {
            for (var _i = 0, _a = this.allowedValues; _i < _a.length; _i++) {
                var allowedValue = _a[_i];
                if (allowedValue === value) {
                    return true;
                }
            }
            return false;
        };
        ValidationRuleFixedList.prototype.validateValue = function (request, fieldId) {
            var value = request.getUnmaskedValue(fieldId);
            return !!value && this.validate(value);
        };
        return ValidationRuleFixedList;
    }());
    return ValidationRuleFixedList;
});
///<amd-module name="connectsdk.ValidationRuleIban"/>
define("connectsdk.ValidationRuleIban", ["require", "exports"], function (require, exports) {
    "use strict";
    /**
     * Sanitize value by remove all unwanted chars of a Iban format
     *
     * @private
     */
    function sanitizeValue(value) {
        return value.replace(/[^\d\w]+/g, "").toUpperCase();
    }
    /**
     * Get state if given value is a valid Iban format
     *
     * @private
     */
    function isValidFormat(value) {
        return typeof value === "string" && /^[A-Z]{2}[0-9]{2}[A-Z0-9]{4}[0-9]{7}([A-Z0-9]?){0,16}$/.test(sanitizeValue(value));
    }
    /**
     * Convert a value to a string needed for validation calculations
     *
     * @private
     */
    function toComputedString(value) {
        return (sanitizeValue(value)
            // place the first 4 chars to the end
            .replace(/(^.{4})(.*)/, "$2$1")
            // replace letters by corresponding numbers A=10 / Z=35
            .replace(/[A-Z]/g, function (d) {
            return (d.charCodeAt(0) - 55).toString();
        }));
    }
    /**
     * Validate Iban by given json
     */
    var ValidationRuleIban = /** @class */ (function () {
        function ValidationRuleIban(json) {
            this.json = json;
            this.type = json.type;
            this.errorMessageId = json.type;
        }
        /**
         * Validate Iban nrule
         *
         * @see https://github.com/arhs/iban.js/blob/master/iban.js
         */
        ValidationRuleIban.prototype.validate = function (value) {
            // bail if format is invalid
            if (!isValidFormat(value)) {
                return false;
            }
            // Check if reminder module 97 equals 1
            // only then it should pass the validation
            var remainder = toComputedString(value);
            while (remainder.length > 2) {
                var block = remainder.slice(0, 9);
                remainder = (parseInt(block, 10) % 97) + remainder.slice(block.length);
            }
            return parseInt(remainder, 10) % 97 === 1;
        };
        ValidationRuleIban.prototype.validateValue = function (request, fieldId) {
            var value = request.getUnmaskedValue(fieldId);
            return !!value && this.validate(value);
        };
        return ValidationRuleIban;
    }());
    return ValidationRuleIban;
});
///<amd-module name="connectsdk.ValidationRuleLength"/>
define("connectsdk.ValidationRuleLength", ["require", "exports"], function (require, exports) {
    "use strict";
    var ValidationRuleLength = /** @class */ (function () {
        function ValidationRuleLength(json) {
            this.json = json;
            this.type = json.type;
            this.errorMessageId = json.type;
            this.minLength = json.attributes.minLength;
            this.maxLength = json.attributes.maxLength;
        }
        ValidationRuleLength.prototype.validate = function (value) {
            return this.minLength <= value.length && value.length <= this.maxLength;
        };
        ValidationRuleLength.prototype.validateValue = function (request, fieldId) {
            var value = request.getUnmaskedValue(fieldId);
            if (!value) {
                // Empty values are allowed if the minimal required length is 0
                return this.minLength === 0;
            }
            return this.validate(value);
        };
        return ValidationRuleLength;
    }());
    return ValidationRuleLength;
});
///<amd-module name="connectsdk.ValidationRuleLuhn"/>
define("connectsdk.ValidationRuleLuhn", ["require", "exports"], function (require, exports) {
    "use strict";
    var ValidationRuleLuhn = /** @class */ (function () {
        function ValidationRuleLuhn(json) {
            this.json = json;
            this.type = json.type;
            this.errorMessageId = json.type;
        }
        ValidationRuleLuhn.prototype.validate = function (value) {
            var luhnArr = [
                [0, 2, 4, 6, 8, 1, 3, 5, 7, 9],
                [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
            ];
            var sum = 0;
            value.replace(/\D+/g, "").replace(/[\d]/g, function (c, p, o) {
                sum += luhnArr[(o.length - p) & 1][parseInt(c, 10)];
                return "";
            });
            return sum % 10 === 0 && sum > 0;
        };
        ValidationRuleLuhn.prototype.validateValue = function (request, fieldId) {
            var value = request.getUnmaskedValue(fieldId);
            return !!value && this.validate(value);
        };
        return ValidationRuleLuhn;
    }());
    return ValidationRuleLuhn;
});
///<amd-module name="connectsdk.ValidationRuleRange"/>
define("connectsdk.ValidationRuleRange", ["require", "exports"], function (require, exports) {
    "use strict";
    var ValidationRuleRange = /** @class */ (function () {
        function ValidationRuleRange(json) {
            this.json = json;
            this.type = json.type;
            this.errorMessageId = json.type;
            this.minValue = json.attributes.minValue;
            this.maxValue = json.attributes.maxValue;
        }
        ValidationRuleRange.prototype.validate = function (value) {
            var intValue = typeof value === "number" ? value : parseInt(value);
            if (isNaN(intValue)) {
                return false;
            }
            return this.minValue <= intValue && intValue <= this.maxValue;
        };
        ValidationRuleRange.prototype.validateValue = function (request, fieldId) {
            var value = request.getUnmaskedValue(fieldId);
            return !!value && this.validate(value);
        };
        return ValidationRuleRange;
    }());
    return ValidationRuleRange;
});
///<amd-module name="connectsdk.ValidationRuleRegularExpression"/>
define("connectsdk.ValidationRuleRegularExpression", ["require", "exports"], function (require, exports) {
    "use strict";
    var ValidationRuleRegularExpression = /** @class */ (function () {
        function ValidationRuleRegularExpression(json) {
            this.json = json;
            this.type = json.type;
            this.errorMessageId = json.type;
            this.regularExpression = json.attributes.regularExpression;
        }
        ValidationRuleRegularExpression.prototype.validate = function (value) {
            var regexp = new RegExp(this.regularExpression);
            return regexp.test(value);
        };
        ValidationRuleRegularExpression.prototype.validateValue = function (request, fieldId) {
            var value = request.getUnmaskedValue(fieldId);
            return !!value && this.validate(value);
        };
        return ValidationRuleRegularExpression;
    }());
    return ValidationRuleRegularExpression;
});
///<amd-module name="connectsdk.ValidationRuleResidentIdNumber"/>
define("connectsdk.ValidationRuleResidentIdNumber", ["require", "exports"], function (require, exports) {
    "use strict";
    var weights = [];
    // https://en.wikipedia.org/wiki/Resident_Identity_Card
    // storing weights in the reverse order so that we can begin
    // from the 0th position of ID while calculating checksum
    for (var i = 18; i > 0; i--) {
        weights.push(Math.pow(2, i - 1) % 11);
    }
    var ValidationRuleResidentIdNumber = /** @class */ (function () {
        function ValidationRuleResidentIdNumber(json) {
            this.json = json;
            this.type = json.type;
            this.errorMessageId = json.type;
        }
        ValidationRuleResidentIdNumber.prototype.validate = function (value) {
            if (value.length < 15) {
                return false;
            }
            if (value.length == 15) {
                return /^\d{15}$/.test(value);
            }
            if (value.length !== 18) {
                return false;
            }
            var sum = 0;
            for (var i = 0; i < value.length - 1; i++) {
                sum += Number(value.charAt(i)) * weights[i];
            }
            var checkSum = (12 - (sum % 11)) % 11;
            var csChar = value.charAt(17);
            if (checkSum < 10) {
                return checkSum == Number(csChar); // check only values
            }
            return !!csChar && csChar.toUpperCase() === "X"; // check the type as well
        };
        ValidationRuleResidentIdNumber.prototype.validateValue = function (request, fieldId) {
            var value = request.getUnmaskedValue(fieldId);
            return !!value && this.validate(value);
        };
        return ValidationRuleResidentIdNumber;
    }());
    return ValidationRuleResidentIdNumber;
});
///<amd-module name="connectsdk.ValidationRuleTermsAndConditions"/>
define("connectsdk.ValidationRuleTermsAndConditions", ["require", "exports"], function (require, exports) {
    "use strict";
    var ValidationRuleTermsAndConditions = /** @class */ (function () {
        function ValidationRuleTermsAndConditions(json) {
            this.json = json;
            this.type = json.type;
            this.errorMessageId = json.type;
        }
        ValidationRuleTermsAndConditions.prototype.validate = function (value) {
            return true === value || "true" === value;
        };
        ValidationRuleTermsAndConditions.prototype.validateValue = function (request, fieldId) {
            var value = request.getUnmaskedValue(fieldId);
            return !!value && this.validate(value);
        };
        return ValidationRuleTermsAndConditions;
    }());
    return ValidationRuleTermsAndConditions;
});
///<amd-module name="connectsdk.ValidationRuleFactory"/>
define("connectsdk.ValidationRuleFactory", ["require", "exports", "connectsdk.ValidationRuleBoletoBancarioRequiredness", "connectsdk.ValidationRuleEmailAddress", "connectsdk.ValidationRuleExpirationDate", "connectsdk.ValidationRuleFixedList", "connectsdk.ValidationRuleIban", "connectsdk.ValidationRuleLength", "connectsdk.ValidationRuleLuhn", "connectsdk.ValidationRuleRange", "connectsdk.ValidationRuleRegularExpression", "connectsdk.ValidationRuleResidentIdNumber", "connectsdk.ValidationRuleTermsAndConditions"], function (require, exports, ValidationRuleBoletoBancarioRequiredness, ValidationRuleEmailAddress, ValidationRuleExpirationDate, ValidationRuleFixedList, ValidationRuleIban, ValidationRuleLength, ValidationRuleLuhn, ValidationRuleRange, ValidationRuleRegularExpression, ValidationRuleResidentIdNumber, ValidationRuleTermsAndConditions) {
    "use strict";
    var validationRules = {
        EmailAddress: ValidationRuleEmailAddress,
        TermsAndConditions: ValidationRuleTermsAndConditions,
        ExpirationDate: ValidationRuleExpirationDate,
        FixedList: ValidationRuleFixedList,
        Length: ValidationRuleLength,
        Luhn: ValidationRuleLuhn,
        Range: ValidationRuleRange,
        RegularExpression: ValidationRuleRegularExpression,
        BoletoBancarioRequiredness: ValidationRuleBoletoBancarioRequiredness,
        Iban: ValidationRuleIban,
        ResidentIdNumber: ValidationRuleResidentIdNumber
    };
    var ValidationRuleFactory = /** @class */ (function () {
        function ValidationRuleFactory() {
        }
        ValidationRuleFactory.prototype.makeValidator = function (json) {
            var rule = json.type.charAt(0).toUpperCase() + json.type.slice(1);
            try {
                return new validationRules[rule](json);
            }
            catch (e) {
                console.warn("no validator for ", rule);
            }
            return null;
        };
        return ValidationRuleFactory;
    }());
    return ValidationRuleFactory;
});
///<amd-module name="connectsdk.DataRestrictions"/>
define("connectsdk.DataRestrictions", ["require", "exports", "connectsdk.ValidationRuleFactory"], function (require, exports, ValidationRuleFactory) {
    "use strict";
    function _parseJSON(_json, _validationRules, _validationRuleByType) {
        var validationRuleFactory = new ValidationRuleFactory();
        if (_json.validators) {
            for (var key in _json.validators) {
                var validationRule = validationRuleFactory.makeValidator({
                    type: key,
                    attributes: _json.validators[key]
                });
                if (validationRule) {
                    _validationRules.push(validationRule);
                    _validationRuleByType[validationRule.type] = validationRule;
                }
            }
        }
    }
    var DataRestrictions = /** @class */ (function () {
        function DataRestrictions(json) {
            this.json = json;
            this.isRequired = json.isRequired;
            this.validationRules = [];
            this.validationRuleByType = {};
            _parseJSON(json, this.validationRules, this.validationRuleByType);
        }
        return DataRestrictions;
    }());
    return DataRestrictions;
});
///<amd-module name="connectsdk.JOSEEncryptor"/>
define("connectsdk.JOSEEncryptor", ["require", "exports", "node-forge"], function (require, exports, forge) {
    "use strict";
    var CEKKEYLENGTH = 512;
    var IVLENGTH = 128;
    function base64UrlEncode(str) {
        str = forge.util.encode64(str);
        str = str.split("=")[0];
        str = str.replace(/\+/g, "-");
        str = str.replace(/\//g, "_");
        return str;
    }
    function createProtectedHeader(kid) {
        var JOSEHeader = {
            alg: "RSA-OAEP",
            enc: "A256CBC-HS512",
            kid: kid
        };
        return JSON.stringify(JOSEHeader);
    }
    function decodePemPublicKey(publickeyB64Encoded) {
        // step 1: base64decode
        var publickeyB64Decoded = forge.util.decode64(publickeyB64Encoded);
        // create a bytebuffer with these bytes
        var buffer2 = forge.util.createBuffer(publickeyB64Decoded, "raw");
        // convert DER to ASN1 object
        var publickeyObject2 = forge.asn1.fromDer(buffer2);
        // convert to publicKey object
        var publicKey2 = forge.pki.publicKeyFromAsn1(publickeyObject2);
        return publicKey2;
    }
    function encryptContentEncryptionKey(CEK, publicKey) {
        // encrypt CEK with OAEP+SHA-1+MGF1Padding
        var encryptedCEK = publicKey.encrypt(CEK, "RSA-OAEP");
        return encryptedCEK;
    }
    function encryptPayload(payload, encKey, initializationVector) {
        var cipher = forge.cipher.createCipher("AES-CBC", encKey);
        cipher.start({
            iv: initializationVector
        });
        cipher.update(forge.util.createBuffer(payload));
        cipher.finish();
        return cipher.output.bytes();
    }
    function calculateAdditionalAuthenticatedDataLength(encodededProtectedHeader) {
        var buffer = forge.util.createBuffer(encodededProtectedHeader);
        var lengthInBits = buffer.length() * 8;
        var buffer2 = forge.util.createBuffer();
        // convert int to 64bit big endian
        buffer2.putInt32(0);
        buffer2.putInt32(lengthInBits);
        return buffer2.bytes();
    }
    function calculateHMAC(macKey, encodededProtectedHeader, initializationVector, cipherText, al) {
        var buffer = forge.util.createBuffer();
        buffer.putBytes(encodededProtectedHeader);
        buffer.putBytes(initializationVector);
        buffer.putBytes(cipherText);
        buffer.putBytes(al);
        var hmacInput = buffer.bytes();
        var hmac = forge.hmac.create();
        hmac.start("sha512", macKey);
        hmac.update(hmacInput);
        return hmac.digest().bytes();
    }
    var JOSEEncryptor = /** @class */ (function () {
        function JOSEEncryptor() {
        }
        // eslint-disable-next-line @typescript-eslint/no-explicit-any
        JOSEEncryptor.prototype.encrypt = function (plainTextValues, publicKeyResponse) {
            // Create protected header and encode it with Base64 encoding
            var payload = JSON.stringify(plainTextValues);
            var protectedHeader = createProtectedHeader(publicKeyResponse.keyId);
            var encodededProtectedHeader = base64UrlEncode(protectedHeader);
            // Create ContentEncryptionKey, is a random byte[]
            var CEK = forge.random.getBytesSync(CEKKEYLENGTH / 8);
            var publicKey = decodePemPublicKey(publicKeyResponse.publicKey);
            // Encrypt the contentEncryptionKey with the GC gateway publickey and encode it with Base64 encoding
            var encryptedContentEncryptionKey = encryptContentEncryptionKey(CEK, publicKey);
            var encodedEncryptedContentEncryptionKey = base64UrlEncode(encryptedContentEncryptionKey);
            // Split the contentEncryptionKey in ENC_KEY and MAC_KEY for using hmac
            var macKey = CEK.substring(0, CEKKEYLENGTH / 2 / 8);
            var encKey = CEK.substring(CEKKEYLENGTH / 2 / 8);
            // Create Initialization Vector
            var initializationVector = forge.random.getBytesSync(IVLENGTH / 8);
            var encodededinitializationVector = base64UrlEncode(initializationVector);
            // Encrypt content with ContentEncryptionKey and Initialization Vector
            var cipherText = encryptPayload(payload, encKey, initializationVector);
            var encodedCipherText = base64UrlEncode(cipherText);
            // Create Additional Authenticated Data  and Additional Authenticated Data Length
            var al = calculateAdditionalAuthenticatedDataLength(encodededProtectedHeader);
            // Calculates HMAC
            var calculatedHmac = calculateHMAC(macKey, encodededProtectedHeader, initializationVector, cipherText, al);
            // Truncate HMAC Value to Create Authentication Tag
            var authenticationTag = calculatedHmac.substring(0, calculatedHmac.length / 2);
            var encodedAuthenticationTag = base64UrlEncode(authenticationTag);
            return encodededProtectedHeader + "." + encodedEncryptedContentEncryptionKey + "." + encodededinitializationVector + "." + encodedCipherText + "." + encodedAuthenticationTag;
        };
        return JOSEEncryptor;
    }());
    return JOSEEncryptor;
});
///<amd-module name="connectsdk.ValueMappingElement"/>
define("connectsdk.ValueMappingElement", ["require", "exports"], function (require, exports) {
    "use strict";
    var ValueMappingElement = /** @class */ (function () {
        function ValueMappingElement(json) {
            this.json = json;
            this.displayName = json.displayName;
            this.value = json.value;
        }
        return ValueMappingElement;
    }());
    return ValueMappingElement;
});
///<amd-module name="connectsdk.FormElement"/>
define("connectsdk.FormElement", ["require", "exports", "connectsdk.ValueMappingElement"], function (require, exports, ValueMappingElement) {
    "use strict";
    function _parseJSON(_json, _valueMapping) {
        if (_json.valueMapping) {
            for (var _i = 0, _a = _json.valueMapping; _i < _a.length; _i++) {
                var mapping = _a[_i];
                _valueMapping.push(new ValueMappingElement(mapping));
            }
        }
    }
    var FormElement = /** @class */ (function () {
        function FormElement(json) {
            this.json = json;
            this.type = json.type;
            this.valueMapping = [];
            _parseJSON(json, this.valueMapping);
        }
        return FormElement;
    }());
    return FormElement;
});
///<amd-module name="connectsdk.Tooltip"/>
define("connectsdk.Tooltip", ["require", "exports"], function (require, exports) {
    "use strict";
    var Tooltip = /** @class */ (function () {
        function Tooltip(json) {
            this.json = json;
            this.image = json.image;
            this.label = json.label;
        }
        return Tooltip;
    }());
    return Tooltip;
});
///<amd-module name="connectsdk.PaymentProductFieldDisplayHints"/>
define("connectsdk.PaymentProductFieldDisplayHints", ["require", "exports", "connectsdk.FormElement", "connectsdk.Tooltip"], function (require, exports, FormElement, Tooltip) {
    "use strict";
    var PaymentProductFieldDisplayHints = /** @class */ (function () {
        function PaymentProductFieldDisplayHints(json) {
            this.json = json;
            this.displayOrder = json.displayOrder;
            if (json.formElement) {
                this.formElement = new FormElement(json.formElement);
            }
            this.label = json.label;
            this.mask = json.mask;
            this.obfuscate = json.obfuscate;
            this.placeholderLabel = json.placeholderLabel;
            this.preferredInputType = json.preferredInputType;
            this.tooltip = json.tooltip ? new Tooltip(json.tooltip) : undefined;
            this.alwaysShow = json.alwaysShow;
            this.wildcardMask = json.mask ? json.mask.replace(/9/g, "*") : "";
        }
        return PaymentProductFieldDisplayHints;
    }());
    return PaymentProductFieldDisplayHints;
});
///<amd-module name="connectsdk.PaymentProductField"/>
define("connectsdk.PaymentProductField", ["require", "exports", "connectsdk.DataRestrictions", "connectsdk.MaskingUtil", "connectsdk.PaymentProductFieldDisplayHints"], function (require, exports, DataRestrictions, MaskingUtil, PaymentProductFieldDisplayHints) {
    "use strict";
    var PaymentProductField = /** @class */ (function () {
        function PaymentProductField(json) {
            var _this = this;
            this.json = json;
            this.displayHints = json.displayHints ? new PaymentProductFieldDisplayHints(json.displayHints) : "";
            this.dataRestrictions = new DataRestrictions(json.dataRestrictions);
            this.id = json.id;
            this.type = json.type;
            var _errorCodes = [];
            this.getErrorCodes = function (value) {
                if (value) {
                    _errorCodes = [];
                    _this.isValid(value);
                }
                return _errorCodes;
            };
            this.getErrorMessageIds = function (request) {
                if (request) {
                    _errorCodes = [];
                    _this.validateValue(request);
                }
                return _errorCodes;
            };
            this.isValid = function (value) {
                // isValid checks all datarestrictions
                var validators = _this.dataRestrictions.validationRules;
                var hasError = false;
                // Apply masking value first
                var maskedValue = _this.applyMask(value);
                value = _this.removeMask(maskedValue.formattedValue);
                for (var _i = 0, validators_1 = validators; _i < validators_1.length; _i++) {
                    var validator = validators_1[_i];
                    if (!validator.validate(value)) {
                        hasError = true;
                        _errorCodes.push(validator.errorMessageId);
                    }
                }
                return !hasError;
            };
            this.validateValue = function (request) {
                // validateValue checks all datarestrictions
                var validators = _this.dataRestrictions.validationRules;
                var hasError = false;
                for (var _i = 0, validators_2 = validators; _i < validators_2.length; _i++) {
                    var validator = validators_2[_i];
                    if (!validator.validateValue(request, _this.id)) {
                        hasError = true;
                        _errorCodes.push(validator.errorMessageId);
                    }
                }
                return !hasError;
            };
        }
        PaymentProductField.prototype.applyMask = function (newValue, oldValue) {
            var maskingUtil = new MaskingUtil();
            var mask = this.displayHints ? this.displayHints.mask : undefined;
            return maskingUtil.applyMask(mask, newValue, oldValue);
        };
        PaymentProductField.prototype.applyWildcardMask = function (newValue, oldValue) {
            var maskingUtil = new MaskingUtil();
            var wildcardMask = this.displayHints ? this.displayHints.wildcardMask : undefined;
            return maskingUtil.applyMask(wildcardMask, newValue, oldValue);
        };
        PaymentProductField.prototype.removeMask = function (value) {
            var maskingUtil = new MaskingUtil();
            var mask = this.displayHints ? this.displayHints.mask : undefined;
            return maskingUtil.removeMask(mask, value);
        };
        return PaymentProductField;
    }());
    return PaymentProductField;
});
///<amd-module name="connectsdk.PaymentProduct"/>
define("connectsdk.PaymentProduct", ["require", "exports", "connectsdk.BasicPaymentProduct", "connectsdk.PaymentProductField"], function (require, exports, BasicPaymentProduct, PaymentProductField) {
    "use strict";
    function _parseJSON(_json, _paymentProductFields, _paymentProductFieldById) {
        if (_json.fields) {
            for (var _i = 0, _a = _json.fields; _i < _a.length; _i++) {
                var field = _a[_i];
                var paymentProductField = new PaymentProductField(field);
                _paymentProductFields.push(paymentProductField);
                _paymentProductFieldById[paymentProductField.id] = paymentProductField;
            }
        }
    }
    var PaymentProduct = /** @class */ (function (_super) {
        __extends(PaymentProduct, _super);
        function PaymentProduct(json) {
            var _this = _super.call(this, json) || this;
            _this.json = json;
            _this.paymentProductFields = [];
            _this.paymentProductFieldById = {};
            _this.fieldsWarning = json.fieldsWarning;
            _parseJSON(json, _this.paymentProductFields, _this.paymentProductFieldById);
            return _this;
        }
        return PaymentProduct;
    }(BasicPaymentProduct));
    return PaymentProduct;
});
///<amd-module name="connectsdk.PaymentRequest"/>
define("connectsdk.PaymentRequest", ["require", "exports"], function (require, exports) {
    "use strict";
    var PaymentRequest = /** @class */ (function () {
        function PaymentRequest(clientSessionID) {
            var _this = this;
            var _clientSessionID = clientSessionID;
            var _fieldValues = {};
            var _paymentProduct;
            var _accountOnFile;
            var _tokenize = false;
            this.setValue = function (paymentProductFieldId, value) {
                _fieldValues[paymentProductFieldId] = value;
            };
            this.setTokenize = function (tokenize) {
                _tokenize = tokenize;
            };
            this.getTokenize = function () {
                return _tokenize;
            };
            this.getErrorMessageIds = function () {
                return _this.validate().map(function (error) { return error.errorMessageId; });
            };
            this.getValue = function (paymentProductFieldId) {
                return _fieldValues[paymentProductFieldId];
            };
            this.getValues = function () {
                return _fieldValues;
            };
            this.getMaskedValue = function (paymentProductFieldId) {
                var paymentProductField = _paymentProduct.paymentProductFieldById[paymentProductFieldId];
                if (paymentProductField) {
                    var value = _this.getValue(paymentProductFieldId);
                    if (typeof value !== "undefined") {
                        var maskedString = paymentProductField.applyMask(value);
                        return maskedString.formattedValue;
                    }
                }
            };
            this.getMaskedValues = function () {
                var result = {};
                for (var paymentProductFieldId in _fieldValues) {
                    var paymentProductField = _paymentProduct.paymentProductFieldById[paymentProductFieldId];
                    var maskedString = paymentProductField.applyMask(_this.getValue(paymentProductFieldId));
                    result[paymentProductFieldId] = maskedString.formattedValue;
                }
                return result;
            };
            this.getUnmaskedValue = function (paymentProductFieldId) {
                var paymentProductField = _paymentProduct.paymentProductFieldById[paymentProductFieldId];
                if (paymentProductField) {
                    var value = _this.getValue(paymentProductFieldId);
                    if (typeof value !== "undefined") {
                        var maskedString = paymentProductField.applyMask(value);
                        var formattedValue = maskedString.formattedValue;
                        return paymentProductField.removeMask(formattedValue);
                    }
                }
            };
            this.getUnmaskedValues = function () {
                var result = {};
                for (var paymentProductFieldId in _fieldValues) {
                    var paymentProductField = _paymentProduct.paymentProductFieldById[paymentProductFieldId];
                    if (paymentProductField) {
                        var maskedString = paymentProductField.applyMask(_this.getValue(paymentProductFieldId));
                        var formattedValue = maskedString.formattedValue;
                        result[paymentProductFieldId] = paymentProductField.removeMask(formattedValue);
                    }
                }
                return result;
            };
            this.setPaymentProduct = function (paymentProduct) {
                _paymentProduct = paymentProduct;
            };
            this.getPaymentProduct = function () {
                return _paymentProduct;
            };
            this.setAccountOnFile = function (accountOnFile) {
                if (accountOnFile) {
                    for (var _i = 0, _a = accountOnFile.attributes; _i < _a.length; _i++) {
                        var attribute = _a[_i];
                        if (attribute.status !== "MUST_WRITE") {
                            delete _fieldValues[attribute.key];
                        }
                    }
                }
                _accountOnFile = accountOnFile || undefined;
            };
            this.getAccountOnFile = function () {
                return _accountOnFile;
            };
            this.getClientSessionID = function () {
                return _clientSessionID;
            };
        }
        PaymentRequest.prototype.isValid = function () {
            return !!this.getPaymentProduct() && this.validate().length === 0;
        };
        /**
         * Validates that the necessary fields are set with correct values.
         * @throws If the payment product has not been set yet.
         */
        PaymentRequest.prototype.validate = function () {
            var paymentProduct = this.getPaymentProduct();
            if (!paymentProduct) {
                throw new Error("Error validating PaymentRequest, please set a paymentProduct first.");
            }
            var errors = [];
            // check fields that are set first
            var fieldValues = this.getValues();
            var _loop_1 = function (key) {
                var paymentProductField = paymentProduct.paymentProductFieldById[key];
                if (paymentProductField) {
                    errors.push.apply(errors, paymentProductField.getErrorMessageIds(this_1).map(function (id) { return ({
                        fieldId: paymentProductField.id,
                        errorMessageId: id
                    }); }));
                }
            };
            var this_1 = this;
            for (var key in fieldValues) {
                _loop_1(key);
            }
            // besides checking the fields for errors, check if all mandatory fields are present as well
            var aof = this.getAccountOnFile();
            if (aof && aof.paymentProductId !== paymentProduct.id) {
                // the account-on-file does not belong to the payment product; ignore it
                aof = undefined;
            }
            var hasValueInAof = function (fieldId) {
                var attribute = aof === null || aof === void 0 ? void 0 : aof.attributeByKey[fieldId];
                return !!attribute && attribute.status !== "MUST_WRITE";
            };
            for (var _i = 0, _a = paymentProduct.paymentProductFields; _i < _a.length; _i++) {
                var field = _a[_i];
                if (field.dataRestrictions.isRequired) {
                    // is this field present in the request?
                    var storedValue = this.getValue(field.id);
                    // if the account on file has the field we can ignore it
                    if (!storedValue && !hasValueInAof(field.id)) {
                        errors.push({
                            fieldId: field.id,
                            errorMessageId: "required"
                        });
                    }
                }
            }
            return errors;
        };
        return PaymentRequest;
    }());
    return PaymentRequest;
});
///<amd-module name="connectsdk.Encryptor"/>
define("connectsdk.Encryptor", ["require", "exports", "node-forge", "connectsdk.JOSEEncryptor", "connectsdk.promise", "connectsdk.Util"], function (require, exports, forge, JOSEEncryptor, Promise, Util) {
    "use strict";
    var util = Util.getInstance();
    function createEncryptedConsumerInput(paymentRequest) {
        var blob = {
            clientSessionId: paymentRequest.getClientSessionID(),
            nonce: forge.util.bytesToHex(forge.random.getBytesSync(16)),
            paymentProductId: paymentRequest.getPaymentProduct().id,
            tokenize: paymentRequest.getTokenize(),
            paymentValues: [],
            collectedDeviceInformation: util.collectDeviceInformation()
        };
        var accountOnFile = paymentRequest.getAccountOnFile();
        if (accountOnFile) {
            blob.accountOnFileId = accountOnFile.id;
        }
        var values = paymentRequest.getUnmaskedValues();
        var ownValues = Object.getOwnPropertyNames(values);
        for (var _i = 0, ownValues_1 = ownValues; _i < ownValues_1.length; _i++) {
            var propertyName = ownValues_1[_i];
            if (propertyName !== "length") {
                blob.paymentValues.push({
                    key: propertyName,
                    value: values[propertyName]
                });
            }
        }
        return blob;
    }
    var Encryptor = /** @class */ (function () {
        function Encryptor(publicKeyResponsePromise) {
            this.encrypt = function (paymentRequest) {
                if (!paymentRequest.getPaymentProduct()) {
                    return Promise.reject("no paymentProduct set");
                }
                var errors = paymentRequest.validate();
                if (errors.length !== 0) {
                    return Promise.reject(errors);
                }
                // paymentRequest is now valid
                var blob = createEncryptedConsumerInput(paymentRequest);
                var promise = new Promise();
                publicKeyResponsePromise.then(function (publicKeyResponse) {
                    // use blob to encrypt
                    var joseEncryptor = new JOSEEncryptor();
                    try {
                        var encryptedString = joseEncryptor.encrypt(blob, publicKeyResponse);
                        promise.resolve(encryptedString);
                    }
                    catch (e) {
                        promise.reject(e);
                    }
                }, function (reason) {
                    promise.reject(reason);
                });
                return promise;
            };
        }
        return Encryptor;
    }());
    return Encryptor;
});
///<amd-module name="connectsdk.PaymentProductGroup"/>
define("connectsdk.PaymentProductGroup", ["require", "exports", "connectsdk.BasicPaymentProductGroup", "connectsdk.PaymentProductField"], function (require, exports, BasicPaymentProductGroup, PaymentProductField) {
    "use strict";
    function _parseJSON(_json, _paymentProductFields, _paymentProductFieldById) {
        if (_json.fields) {
            for (var _i = 0, _a = _json.fields; _i < _a.length; _i++) {
                var field = _a[_i];
                var paymentProductField = new PaymentProductField(field);
                _paymentProductFields.push(paymentProductField);
                _paymentProductFieldById[paymentProductField.id] = paymentProductField;
            }
        }
    }
    var PaymentProductGroup = /** @class */ (function (_super) {
        __extends(PaymentProductGroup, _super);
        function PaymentProductGroup(json) {
            var _this = _super.call(this, json) || this;
            _this.json = json;
            _this.paymentProductFields = [];
            _this.paymentProductFieldById = {};
            _parseJSON(json, _this.paymentProductFields, _this.paymentProductFieldById);
            return _this;
        }
        return PaymentProductGroup;
    }(BasicPaymentProductGroup));
    return PaymentProductGroup;
});
///<amd-module name="connectsdk.PaymentItem"/>
define("connectsdk.PaymentItem", ["require", "exports"], function (require, exports) {
    "use strict";
    exports.__esModule = true;
});
///<amd-module name="connectsdk.Session"/>
define("connectsdk.Session", ["require", "exports", "connectsdk.BasicPaymentItems", "connectsdk.BasicPaymentProductGroups", "connectsdk.BasicPaymentProducts", "connectsdk.C2SCommunicator", "connectsdk.C2SCommunicatorConfiguration", "connectsdk.C2SPaymentProductContext", "connectsdk.Encryptor", "connectsdk.PaymentProduct", "connectsdk.PaymentProductGroup", "connectsdk.PaymentRequest", "connectsdk.promise"], function (require, exports, BasicPaymentItems, BasicPaymentProductGroups, BasicPaymentProducts, C2SCommunicator, C2SCommunicatorConfiguration, C2SPaymentProductContext, Encryptor, PaymentProduct, PaymentProductGroup, PaymentRequest, Promise) {
    "use strict";
    var APIVERSION = "client/v1";
    // Keep the lowercase name for backward compatibility
    // eslint-disable-next-line @typescript-eslint/naming-convention
    var session = /** @class */ (function () {
        function session(sessionDetails, paymentProduct) {
            var _this = this;
            var _c2SCommunicatorConfiguration = new C2SCommunicatorConfiguration(sessionDetails, APIVERSION);
            var _c2sCommunicator = new C2SCommunicator(_c2SCommunicatorConfiguration, paymentProduct);
            //_paymentRequest,
            this.clientApiUrl = _c2SCommunicatorConfiguration.clientApiUrl;
            this.assetUrl = _c2SCommunicatorConfiguration.assetUrl;
            var _paymentDetails;
            var _paymentProduct;
            var _paymentProductGroup;
            var _paymentRequest;
            this.getBasicPaymentProducts = function (paymentDetails, paymentProductSpecificInputs) {
                var promise = new Promise();
                var c2SPaymentProductContext = new C2SPaymentProductContext(paymentDetails);
                _c2sCommunicator.getBasicPaymentProducts(c2SPaymentProductContext, paymentProductSpecificInputs).then(function (json) {
                    _paymentDetails = paymentDetails;
                    var paymentProducts = new BasicPaymentProducts(json);
                    promise.resolve(paymentProducts);
                }, function (reason) {
                    promise.reject(reason);
                });
                return promise;
            };
            this.getBasicPaymentProductGroups = function (paymentDetails) {
                var promise = new Promise();
                var c2SPaymentProductContext = new C2SPaymentProductContext(paymentDetails);
                _c2sCommunicator.getBasicPaymentProductGroups(c2SPaymentProductContext).then(function (json) {
                    _paymentDetails = paymentDetails;
                    var paymentProductGroups = new BasicPaymentProductGroups(json);
                    promise.resolve(paymentProductGroups);
                }, function (reason) {
                    promise.reject(reason);
                });
                return promise;
            };
            this.getBasicPaymentItems = function (paymentDetails, useGroups, paymentProductSpecificInputs) {
                var promise = new Promise();
                // get products & groups
                if (useGroups) {
                    _this.getBasicPaymentProducts(paymentDetails, paymentProductSpecificInputs).then(function (products) {
                        _this.getBasicPaymentProductGroups(paymentDetails).then(function (groups) {
                            var basicPaymentItems = new BasicPaymentItems(products, groups);
                            promise.resolve(basicPaymentItems);
                        }, function (reason) {
                            promise.reject(reason);
                        });
                    }, function (reason) {
                        promise.reject(reason);
                    });
                }
                else {
                    _this.getBasicPaymentProducts(paymentDetails, paymentProductSpecificInputs).then(function (products) {
                        var basicPaymentItems = new BasicPaymentItems(products, null);
                        promise.resolve(basicPaymentItems);
                    }, function (reason) {
                        promise.reject(reason);
                    });
                }
                return promise;
            };
            this.getPaymentProduct = function (paymentProductId, paymentDetails, paymentProductSpecificInputs) {
                var promise = new Promise();
                var c2SPaymentProductContext = new C2SPaymentProductContext(_paymentDetails || paymentDetails);
                _c2sCommunicator.getPaymentProduct(paymentProductId, c2SPaymentProductContext, paymentProductSpecificInputs).then(function (response) {
                    _paymentProduct = new PaymentProduct(response);
                    promise.resolve(_paymentProduct);
                }, function (reason) {
                    _paymentProduct = null;
                    promise.reject(reason);
                });
                return promise;
            };
            this.getPaymentProductGroup = function (paymentProductGroupId, paymentDetails) {
                var promise = new Promise();
                var c2SPaymentProductContext = new C2SPaymentProductContext(_paymentDetails || paymentDetails);
                _c2sCommunicator.getPaymentProductGroup(paymentProductGroupId, c2SPaymentProductContext).then(function (response) {
                    _paymentProductGroup = new PaymentProductGroup(response);
                    promise.resolve(_paymentProductGroup);
                }, function (reason) {
                    _paymentProductGroup = null;
                    promise.reject(reason);
                });
                return promise;
            };
            this.getIinDetails = function (partialCreditCardNumber, paymentDetails) {
                partialCreditCardNumber = partialCreditCardNumber.replace(/ /g, "");
                if (partialCreditCardNumber.length >= 8) {
                    partialCreditCardNumber = partialCreditCardNumber.substring(0, 8);
                }
                else {
                    partialCreditCardNumber = partialCreditCardNumber.substring(0, 6);
                }
                var c2SPaymentProductContext = new C2SPaymentProductContext(_paymentDetails || paymentDetails);
                return _c2sCommunicator.getPaymentProductIdByCreditCardNumber(partialCreditCardNumber, c2SPaymentProductContext);
            };
            this.getPublicKey = function () {
                return _c2sCommunicator.getPublicKey();
            };
            this.getPaymentProductNetworks = function (paymentProductId, paymentDetails) {
                var promise = new Promise();
                var c2SPaymentProductContext = new C2SPaymentProductContext(paymentDetails);
                _c2sCommunicator.getPaymentProductNetworks(paymentProductId, c2SPaymentProductContext).then(function (response) {
                    _paymentDetails = paymentDetails;
                    promise.resolve(response);
                }, function (reason) {
                    promise.reject(reason);
                });
                return promise;
            };
            this.getPaymentProductDirectory = function (paymentProductId, currencyCode, countryCode) {
                return _c2sCommunicator.getPaymentProductDirectory(paymentProductId, currencyCode, countryCode);
            };
            this.convertAmount = function (amount, source, target) {
                return _c2sCommunicator.convertAmount(amount, source, target);
            };
            this.getPaymentRequest = function () {
                if (!_paymentRequest) {
                    _paymentRequest = new PaymentRequest(_c2SCommunicatorConfiguration.clientSessionId);
                }
                return _paymentRequest;
            };
            this.getEncryptor = function () {
                var publicKeyResponsePromise = _c2sCommunicator.getPublicKey();
                return new Encryptor(publicKeyResponsePromise);
            };
            this.getThirdPartyPaymentStatus = function (paymentId) {
                return _c2sCommunicator.getThirdPartyPaymentStatus(paymentId);
            };
            this.getCustomerDetails = function (paymentProductId, paymentDetails) {
                return _c2sCommunicator.getCustomerDetails(paymentProductId, paymentDetails);
            };
            this.createPaymentProductSession = function (paymentProductId, paymentDetails) {
                return _c2sCommunicator.createPaymentProductSession(paymentProductId, paymentDetails);
            };
            this.createApplePayPayment = function (context, paymentProductSpecificInput, networks) {
                return _c2sCommunicator.initApplePayPayment(context, paymentProductSpecificInput, networks);
            };
            this.transformPaymentProductJSON = function (json) {
                return new PaymentProduct(_c2sCommunicator.transformPaymentProductJSON(json));
            };
            this.transformPaymentProductGroupJSON = function (json) {
                return new PaymentProductGroup(_c2sCommunicator.transformPaymentProductJSON(json));
            };
        }
        return session;
    }());
    return session;
});
///<amd-module name="connectsdk.core"/>
define("connectsdk.core", ["require", "exports", "connectsdk.AccountOnFile", "connectsdk.AccountOnFileDisplayHints", "connectsdk.ApplePay", "connectsdk.Attribute", "connectsdk.BasicPaymentItems", "connectsdk.BasicPaymentProduct", "connectsdk.BasicPaymentProductGroup", "connectsdk.BasicPaymentProductGroups", "connectsdk.BasicPaymentProducts", "connectsdk.C2SCommunicator", "connectsdk.C2SCommunicatorConfiguration", "connectsdk.C2SPaymentProductContext", "connectsdk.DataRestrictions", "connectsdk.Encryptor", "connectsdk.FormElement", "connectsdk.GooglePay", "connectsdk.IinDetailsResponse", "connectsdk.JOSEEncryptor", "connectsdk.LabelTemplateElement", "connectsdk.MaskedString", "connectsdk.MaskingUtil", "connectsdk.net", "connectsdk.PaymentProduct", "connectsdk.PaymentProduct302SpecificData", "connectsdk.PaymentProduct320SpecificData", "connectsdk.PaymentProduct863SpecificData", "connectsdk.PaymentProductField", "connectsdk.PaymentProductFieldDisplayHints", "connectsdk.PaymentProductGroup", "connectsdk.PaymentRequest", "connectsdk.promise", "connectsdk.PublicKeyResponse", "connectsdk.Session", "connectsdk.Tooltip", "connectsdk.Util", "connectsdk.ValidationRuleBoletoBancarioRequiredness", "connectsdk.ValidationRuleEmailAddress", "connectsdk.ValidationRuleExpirationDate", "connectsdk.ValidationRuleFactory", "connectsdk.ValidationRuleFixedList", "connectsdk.ValidationRuleIban", "connectsdk.ValidationRuleLength", "connectsdk.ValidationRuleLuhn", "connectsdk.ValidationRuleRange", "connectsdk.ValidationRuleRegularExpression", "connectsdk.ValidationRuleResidentIdNumber", "connectsdk.ValidationRuleTermsAndConditions", "connectsdk.ValueMappingElement"], function (require, exports, _AccountOnFile, _AccountOnFileDisplayHints, _ApplePay, _Attribute, _BasicPaymentItems, _BasicPaymentProduct, _BasicPaymentProductGroup, _BasicPaymentProductGroups, _BasicPaymentProducts, _C2SCommunicator, _C2SCommunicatorConfiguration, _C2SPaymentProductContext, _DataRestrictions, _Encryptor, _FormElement, _GooglePay, _IinDetailsResponse, _JOSEEncryptor, _LabelTemplateElement, _MaskedString, _MaskingUtil, _Net, _PaymentProduct, _PaymentProduct302SpecificData, _PaymentProduct320SpecificData, _PaymentProduct863SpecificData, _PaymentProductField, _PaymentProductFieldDisplayHints, _PaymentProductGroup, _PaymentRequest, _Promise, _PublicKeyResponse, _session, _Tooltip, _Util, _ValidationRuleBoletoBancarioRequiredness, _ValidationRuleEmailAddress, _ValidationRuleExpirationDate, _ValidationRuleFactory, _ValidationRuleFixedList, _ValidationRuleIban, _ValidationRuleLength, _ValidationRuleLuhn, _ValidationRuleRange, _ValidationRuleRegularExpression, _ValidationRuleResidentIdNumber, _ValidationRuleTermsAndConditions, _ValueMappingElement) {
    "use strict";
    var connectsdk = {
        AccountOnFile: _AccountOnFile,
        AccountOnFileDisplayHints: _AccountOnFileDisplayHints,
        ApplePay: _ApplePay,
        Attribute: _Attribute,
        BasicPaymentItems: _BasicPaymentItems,
        BasicPaymentProduct: _BasicPaymentProduct,
        BasicPaymentProductGroup: _BasicPaymentProductGroup,
        BasicPaymentProductGroups: _BasicPaymentProductGroups,
        BasicPaymentProducts: _BasicPaymentProducts,
        C2SCommunicator: _C2SCommunicator,
        C2SCommunicatorConfiguration: _C2SCommunicatorConfiguration,
        C2SPaymentProductContext: _C2SPaymentProductContext,
        DataRestrictions: _DataRestrictions,
        Encryptor: _Encryptor,
        FormElement: _FormElement,
        GooglePay: _GooglePay,
        IinDetailsResponse: _IinDetailsResponse,
        JOSEEncryptor: _JOSEEncryptor,
        LabelTemplateElement: _LabelTemplateElement,
        MaskedString: _MaskedString,
        MaskingUtil: _MaskingUtil,
        net: _Net,
        get: _Net.get,
        post: _Net.post,
        jsonp: _Net.jsonp,
        PaymentProduct: _PaymentProduct,
        PaymentProduct302SpecificData: _PaymentProduct302SpecificData,
        PaymentProduct320SpecificData: _PaymentProduct320SpecificData,
        PaymentProduct863SpecificData: _PaymentProduct863SpecificData,
        PaymentProductField: _PaymentProductField,
        PaymentProductFieldDisplayHints: _PaymentProductFieldDisplayHints,
        PaymentProductGroup: _PaymentProductGroup,
        PaymentRequest: _PaymentRequest,
        Promise: _Promise,
        PublicKeyResponse: _PublicKeyResponse,
        Session: _session,
        Tooltip: _Tooltip,
        Util: _Util,
        ValidationRuleBoletoBancarioRequiredness: _ValidationRuleBoletoBancarioRequiredness,
        ValidationRuleEmailAddress: _ValidationRuleEmailAddress,
        ValidationRuleExpirationDate: _ValidationRuleExpirationDate,
        ValidationRuleFactory: _ValidationRuleFactory,
        ValidationRuleFixedList: _ValidationRuleFixedList,
        ValidationRuleIban: _ValidationRuleIban,
        ValidationRuleLength: _ValidationRuleLength,
        ValidationRuleLuhn: _ValidationRuleLuhn,
        ValidationRuleRange: _ValidationRuleRange,
        ValidationRuleRegularExpression: _ValidationRuleRegularExpression,
        ValidationRuleResidentIdNumber: _ValidationRuleResidentIdNumber,
        ValidationRuleTermsAndConditions: _ValidationRuleTermsAndConditions,
        ValueMappingElement: _ValueMappingElement
    };
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    var global = (typeof window === "undefined" ? this : window);
    global.connectsdk = connectsdk;
    return connectsdk;
});

//# sourceMappingURL=index.js.map
