/// <reference types="applepayjs" />
/// <amd-module name="connectsdk.apimodel" />
declare module "connectsdk.apimodel" {
    export interface AccountOnFileJSON {
        attributes: AccountOnFileAttributeJSON[];
        displayHints: AccountOnFileDisplayHintsJSON;
        id: number;
        paymentProductId: number;
    }
    export interface AccountOnFileAttributeJSON extends KeyValuePairJSON {
        mustWriteReason?: string;
        status: string;
    }
    export interface AccountOnFileDisplayHintsJSON {
        labelTemplate: LabelTemplateElementJSON[];
        logo: string;
    }
    export interface AuthenticationIndicatorJSON {
        name: string;
        value: string;
    }
    export interface BasicPaymentProductJSON {
        accountsOnFile?: AccountOnFileJSON[];
        acquirerCountry?: string;
        allowsInstallments: boolean;
        allowsRecurring: boolean;
        allowsTokenization: boolean;
        authenticationIndicator?: AuthenticationIndicatorJSON;
        autoTokenized: boolean;
        canBeIframed?: boolean;
        deviceFingerprintEnabled: boolean;
        displayHints: PaymentProductDisplayHintsJSON;
        id: number;
        isJavaScriptRequired?: boolean;
        maxAmount?: number;
        minAmount?: number;
        mobileIntegrationLevel: string;
        paymentMethod: string;
        paymentProduct302SpecificData?: PaymentProduct302SpecificDataJSON;
        paymentProduct320SpecificData?: PaymentProduct320SpecificDataJSON;
        paymentProduct863SpecificData?: PaymentProduct863SpecificDataJSON;
        paymentProductGroup?: string;
        supportsMandates?: boolean;
        usesRedirectionTo3rdParty: boolean;
        type?: "product";
    }
    export interface BasicPaymentProductGroupJSON {
        accountsOnFile?: AccountOnFileJSON[];
        allowsInstallments: boolean;
        deviceFingerprintEnabled: boolean;
        displayHints: PaymentProductDisplayHintsJSON;
        id: string;
        type?: "group";
    }
    export interface CreatePaymentProductSessionRequestJSON {
        paymentProductSession302SpecificInput?: MobilePaymentProductSession302SpecificInputJSON;
    }
    export interface CreatePaymentProductSessionResponseJSON {
        paymentProductSession302SpecificOutput?: MobilePaymentProductSession302SpecificOutputJSON;
    }
    export interface DirectoryJSON {
        entries: DirectoryEntryJSON[];
    }
    export interface DirectoryEntryJSON {
        countryNames?: string[];
        issuerId: string;
        issuerList?: string;
        issuerName: string;
    }
    export interface KeyValuePairJSON {
        key: string;
        value: string;
    }
    export interface LabelTemplateElementJSON {
        attributeKey: string;
        mask: string;
    }
    export interface MobilePaymentProductSession302SpecificInputJSON {
        displayName?: string;
        domainName?: string;
        validationUrl?: string;
    }
    export interface MobilePaymentProductSession302SpecificOutputJSON {
        sessionObject: string;
    }
    export interface PaymentProductJSON extends BasicPaymentProductJSON {
        fields: PaymentProductFieldJSON[];
        fieldsWarning?: string;
    }
    export interface PaymentProductDisplayHintsJSON {
        displayOrder: number;
        label?: string;
        logo: string;
    }
    export interface PaymentProduct302SpecificDataJSON {
        networks: string[];
    }
    export interface PaymentProduct320SpecificDataJSON {
        gateway: string;
        networks: string[];
    }
    export interface PaymentProduct863SpecificDataJSON {
        integrationTypes: string[];
    }
    export interface PaymentProductFieldJSON {
        dataRestrictions: PaymentProductFieldDataRestrictionsJSON;
        displayHints?: PaymentProductFieldDisplayHintsJSON;
        id: string;
        type: string;
        usedForLookup?: boolean;
        validators?: string[];
    }
    export interface PaymentProductFieldDataRestrictionsJSON {
        isRequired: boolean;
        validators: PaymentProductFieldValidatorsJSON;
    }
    export interface PaymentProductFieldDisplayElementJSON {
        id: string;
        label?: string;
        type: string;
        value: string;
    }
    export interface PaymentProductFieldDisplayHintsJSON {
        alwaysShow: boolean;
        displayOrder: number;
        formElement: PaymentProductFieldFormElementJSON;
        label?: string;
        link?: string;
        mask?: string;
        obfuscate: boolean;
        placeholderLabel?: string;
        preferredInputType?: string;
        tooltip?: PaymentProductFieldTooltipJSON;
    }
    export interface PaymentProductFieldFormElementJSON {
        type: string;
        valueMapping?: ValueMappingElementJSON[];
        list?: boolean;
    }
    export interface PaymentProductFieldTooltipJSON {
        image: string;
        label?: string;
    }
    export interface PaymentProductFieldValidatorsJSON {
        boletoBancarioRequiredness?: BoletoBancarioRequirednessValidatorJSON;
        emailAddress?: EmptyValidatorJSON;
        expirationDate?: EmptyValidatorJSON;
        fixedList?: FixedListValidatorJSON;
        iban?: EmptyValidatorJSON;
        length?: LengthValidatorJSON;
        luhn?: EmptyValidatorJSON;
        range?: RangeValidatorJSON;
        regularExpression?: RegularExpressionValidatorJSON;
        residentIdNumber?: EmptyValidatorJSON;
        termsAndConditions?: EmptyValidatorJSON;
    }
    export interface PaymentProductGroupJSON extends BasicPaymentProductGroupJSON {
        fields: PaymentProductFieldJSON[];
    }
    export interface PaymentProductGroupsJSON {
        paymentProductGroups: BasicPaymentProductGroupJSON[];
    }
    export interface PaymentProductNetworksResponseJSON {
        networks: string[];
    }
    export interface PaymentProductsJSON {
        paymentProducts: BasicPaymentProductJSON[];
    }
    export interface ValueMappingElementJSON {
        displayElements: PaymentProductFieldDisplayElementJSON[];
        displayName?: string;
        value: string;
    }
    export interface BoletoBancarioRequirednessValidatorJSON {
        fiscalNumberLength: number;
    }
    export interface EmptyValidatorJSON {
    }
    export interface FixedListValidatorJSON {
        allowedValues: string[];
    }
    export interface LengthValidatorJSON {
        minLength: number;
        maxLength: number;
    }
    export interface RangeValidatorJSON {
        minValue: number;
        maxValue: number;
    }
    export interface RegularExpressionValidatorJSON {
        regularExpression: string;
    }
    export interface PublicKeyJSON {
        keyId: string;
        publicKey: string;
    }
    export interface AmountOfMoneyJSON {
        amount: number;
        currencyCode: string;
    }
    export interface GetIINDetailsRequestJSON {
        bin: string;
        paymentContext?: PaymentContextJSON;
    }
    export interface GetIINDetailsResponseJSON {
        coBrands?: IinDetailJSON[];
        countryCode: string;
        isAllowedInContext?: boolean;
        paymentProductId: number;
    }
    export interface IinDetailJSON {
        isAllowedInContext: boolean;
        paymentProductId: number;
    }
    export interface PaymentContextJSON {
        amountOfMoney: AmountOfMoneyJSON;
        countryCode: string;
        isInstallments?: boolean;
        isRecurring?: boolean;
    }
    export interface ConvertAmountJSON {
        convertedAmount: number;
    }
    export interface GetCustomerDetailsRequestJSON {
        countryCode: string;
        values: KeyValuePairJSON[];
    }
    export interface GetCustomerDetailsResponseJSON {
        city?: string;
        country?: string;
        emailAddress?: string;
        firstName?: string;
        fiscalNumber?: string;
        languageCode?: string;
        phoneNumber?: string;
        street?: string;
        surname?: string;
        zip?: string;
    }
    export interface ThirdPartyStatusResponseJSON {
        thirdPartyStatus: string;
    }
    export interface APIErrorJSON {
        category: string;
        code: string;
        httpStatusCode: number;
        id: string;
        message: string;
        propertyName: string;
        requestId: string;
    }
    export interface ErrorResponseJSON {
        errorId: string;
        errors: APIErrorJSON[];
    }
}
/// <amd-module name="connectsdk.LabelTemplateElement" />
declare module "connectsdk.LabelTemplateElement" {
    import { LabelTemplateElementJSON } from "connectsdk.apimodel";
    class LabelTemplateElement {
        readonly json: LabelTemplateElementJSON;
        readonly attributeKey: string;
        readonly mask: string;
        readonly wildcardMask: string;
        constructor(json: LabelTemplateElementJSON);
    }
    export = LabelTemplateElement;
}
/// <amd-module name="connectsdk.AccountOnFileDisplayHints" />
declare module "connectsdk.AccountOnFileDisplayHints" {
    import { AccountOnFileDisplayHintsJSON } from "connectsdk.apimodel";
    import LabelTemplateElement = require("connectsdk.LabelTemplateElement");
    class AccountOnFileDisplayHints {
        readonly json: AccountOnFileDisplayHintsJSON;
        readonly logo: string;
        readonly labelTemplate: LabelTemplateElement[];
        readonly labelTemplateElementByAttributeKey: {
            [attributeKey: string]: LabelTemplateElement | undefined;
        };
        constructor(json: AccountOnFileDisplayHintsJSON);
    }
    export = AccountOnFileDisplayHints;
}
/// <amd-module name="connectsdk.Attribute" />
declare module "connectsdk.Attribute" {
    import { AccountOnFileAttributeJSON } from "connectsdk.apimodel";
    class Attribute {
        readonly json: AccountOnFileAttributeJSON;
        readonly key: string;
        readonly value: string;
        readonly status: string;
        readonly mustWriteReason?: string;
        constructor(json: AccountOnFileAttributeJSON);
    }
    export = Attribute;
}
/// <amd-module name="connectsdk.MaskedString" />
declare module "connectsdk.MaskedString" {
    class MaskedString {
        readonly formattedValue: string;
        readonly cursorIndex: number;
        constructor(formattedValue: string, cursorIndex: number);
    }
    export = MaskedString;
}
/// <amd-module name="connectsdk.MaskingUtil" />
declare module "connectsdk.MaskingUtil" {
    import MaskedString = require("connectsdk.MaskedString");
    class MaskingUtil {
        applyMask(mask: string | undefined, newValue: string, oldValue?: string): MaskedString;
        getMaxLengthBasedOnMask(mask?: string): number;
        removeMask(mask: string | undefined, value: string): string;
    }
    export = MaskingUtil;
}
/// <amd-module name="connectsdk.AccountOnFile" />
declare module "connectsdk.AccountOnFile" {
    import AccountOnFileDisplayHints = require("connectsdk.AccountOnFileDisplayHints");
    import { AccountOnFileJSON } from "connectsdk.apimodel";
    import Attribute = require("connectsdk.Attribute");
    import MaskedString = require("connectsdk.MaskedString");
    class AccountOnFile {
        readonly json: AccountOnFileJSON;
        readonly attributes: Attribute[];
        readonly attributeByKey: {
            [key: string]: Attribute | undefined;
        };
        readonly displayHints: AccountOnFileDisplayHints;
        readonly id: number;
        readonly paymentProductId: number;
        constructor(json: AccountOnFileJSON);
        getMaskedValueByAttributeKey(attributeKey: string): MaskedString | undefined;
    }
    export = AccountOnFile;
}
/// <amd-module name="connectsdk.promise" />
declare module "connectsdk.promise" {
    /**
     * The Promise class.
     */
    class Promise<T> {
        readonly isSingleton: boolean;
        /**
         * Resolves a promise.
         */
        readonly resolve: (result: T) => void;
        /**
         * Rejects a promise.
         */
        readonly reject: (result: any) => void;
        /**
         * Adds a success and failure handler for completion of this Promise object.
         *
         * @param {Function} success The success handler
         * @param {Function} failure The failure handler
         * @returns {Promise} `this`
         */
        readonly then: (success: (result: T) => void, failure?: (result: any) => void) => Promise<T>;
        constructor(singleton?: boolean);
        static resolve<T>(value: T): Promise<T>;
        static reject<T>(reason: any): Promise<T>;
    }
    export = Promise;
}
/// <amd-module name="connectsdk.types" />
declare module "connectsdk.types" {
    import { PaymentProductsJSON } from "connectsdk.apimodel";
    export interface PaymentProductSessionContext {
        displayName: string;
        domainName: string;
        validationURL: string;
    }
    export interface PaymentProductSpecificInputs {
        bancontact?: BancontactSpecificInput;
        applePay?: ApplePaySpecificInput;
        googlePay?: GooglePaySpecificInput;
    }
    export interface BancontactSpecificInput {
        forceBasicFlow: boolean;
    }
    export interface ApplePaySpecificInput {
        merchantName: string;
        acquirerCountry?: string;
    }
    export interface GooglePaySpecificInput {
        merchantId: string;
        gatewayMerchantId: string;
        merchantName?: string;
    }
    export interface PaymentDetails {
        totalAmount: number;
        countryCode: string;
        isRecurring: boolean;
        currency: string;
        locale?: string;
        isInstallments?: boolean;
        accountOnFileId?: number;
        environment?: string | "PROD";
    }
    export interface SessionDetails {
        clientSessionId: string;
        assetUrl: string;
        clientApiUrl: string;
        customerId: string;
    }
    export interface ApplePayPaymentDetails extends PaymentDetails {
        displayName: string;
        acquirerCountry?: string;
        networks: string[];
    }
    export interface ApplePayInitResult {
        message: string;
        data: ApplePayJS.ApplePayPaymentToken;
    }
    export type IinDetailsStatus = "SUPPORTED" | "EXISTING_BUT_NOT_ALLOWED" | "UNSUPPORTED" | "UNKNOWN" | "NOT_ENOUGH_DIGITS";
    export interface ValidatableRequest {
        getValue(paymentProductFieldId: string): string | undefined;
        getMaskedValue(paymentProductFieldId: string): string | undefined;
        getUnmaskedValue(paymentProductFieldId: string): string | undefined;
    }
    export interface ValidationError {
        fieldId: string;
        errorMessageId: string;
    }
    export interface ValidationRule {
        readonly json: ValidationRuleDefinition<unknown>;
        readonly type: string;
        readonly errorMessageId: string;
        validate(value: string): boolean;
        validateValue(request: ValidatableRequest, fieldId: string): boolean;
    }
    export interface ValidationRuleDefinition<T> {
        readonly type: string;
        readonly attributes: T;
    }
    export interface Metadata {
        readonly screenSize: string;
        readonly platformIdentifier: string;
        readonly sdkIdentifier: string;
        readonly sdkCreator: string;
    }
    export interface BrowserData {
        readonly javaScriptEnabled: true;
        readonly javaEnabled: boolean;
        readonly colorDepth: number;
        readonly screenHeight: number;
        readonly screenWidth: number;
        readonly innerHeight: number;
        readonly innerWidth: number;
    }
    export interface DeviceInformation {
        readonly timezoneOffsetUtcMinutes: number;
        readonly locale: string;
        readonly browserData: BrowserData;
    }
    export interface Util {
        readonly applePayPaymentProductId: 302;
        readonly googlePayPaymentProductId: 320;
        readonly bancontactPaymentProductId: 3012;
        readonly paymentProductsThatAreNotSupportedInThisBrowser: number[];
        getMetadata(): Metadata;
        collectDeviceInformation(): DeviceInformation;
        base64Encode(data: object | string): string;
        filterOutProductsThatAreNotSupportedInThisBrowser(json: PaymentProductsJSON): void;
    }
    export interface AjaxRequestOptions {
        /** HTTP method - GET, POST, etc. */
        method?: string;
        /** Headers */
        headers?: {
            [key: string]: string | undefined;
        };
        /** A callback to run when a request is successful */
        success?: (response: SdkResponse, request: XMLHttpRequest) => void;
        /** A callback to run when the request fails */
        error?: (response?: SdkResponse, request?: XMLHttpRequest) => void;
        /** A callback to run when a request completes, successful or not */
        callback?: (response: SdkResponse, request: XMLHttpRequest) => void;
        /** Defaults to asynchronous */
        asynchronous?: boolean;
        /** The HTTP POST body */
        postBody?: string;
        /** The content type of the request, default is `application/x-www-form-urlencoded` */
        contentType?: string;
    }
    export interface AjaxRequest {
        set(key: string, value: string): AjaxRequest;
        send(data: object | string, callback: (response: SdkResponse, request: XMLHttpRequest) => void): AjaxRequest;
        end(callback?: (response: SdkResponse, request: XMLHttpRequest) => void): AjaxRequest;
        data(data: object | string): AjaxRequest;
        then(success: (result: SdkResponse) => void, failure?: (result: any) => void): AjaxRequest;
    }
    export interface JSONPOptions {
        success: (json: unknown) => void;
        failure?: () => void;
    }
    export interface SdkResponse {
        status: number;
        responseText: string;
        responseJSON?: unknown;
        responseXML?: unknown;
        success: boolean;
    }
}
/// <amd-module name="connectsdk.Util" />
declare module "connectsdk.Util" {
    import { Util } from "connectsdk.types";
    const _default: {
        getInstance(): Util;
    };
    export = _default;
}
/// <amd-module name="connectsdk.ApplePay" />
declare module "connectsdk.ApplePay" {
    import Promise = require("connectsdk.promise");
    import { CreatePaymentProductSessionResponseJSON } from "connectsdk.apimodel";
    import { ApplePayInitResult, ApplePayPaymentDetails, PaymentProductSessionContext } from "connectsdk.types";
    interface ApplePayC2SCommunicator {
        createPaymentProductSession(paymentProductId: number, context: PaymentProductSessionContext): Promise<CreatePaymentProductSessionResponseJSON>;
    }
    class ApplePay {
        isApplePayAvailable(): boolean;
        initPayment(context: ApplePayPaymentDetails, c2SCommunicator: ApplePayC2SCommunicator): Promise<ApplePayInitResult>;
    }
    export = ApplePay;
}
/// <amd-module name="connectsdk.AuthenticationIndicator" />
declare module "connectsdk.AuthenticationIndicator" {
    import { AuthenticationIndicatorJSON } from "connectsdk.apimodel";
    class AuthenticationIndicator {
        readonly json: AuthenticationIndicatorJSON;
        readonly name: string;
        readonly value: string;
        constructor(json: AuthenticationIndicatorJSON);
    }
    export = AuthenticationIndicator;
}
/// <amd-module name="connectsdk.PaymentProduct302SpecificData" />
declare module "connectsdk.PaymentProduct302SpecificData" {
    import { PaymentProduct302SpecificDataJSON } from "connectsdk.apimodel";
    class PaymentProduct302SpecificData {
        readonly json: PaymentProduct302SpecificDataJSON;
        readonly networks: string[];
        constructor(json: PaymentProduct302SpecificDataJSON);
    }
    export = PaymentProduct302SpecificData;
}
/// <amd-module name="connectsdk.PaymentProduct320SpecificData" />
declare module "connectsdk.PaymentProduct320SpecificData" {
    import { PaymentProduct320SpecificDataJSON } from "connectsdk.apimodel";
    class PaymentProduct320SpecificData {
        readonly json: PaymentProduct320SpecificDataJSON;
        readonly gateway: string;
        readonly networks: string[];
        constructor(json: PaymentProduct320SpecificDataJSON);
    }
    export = PaymentProduct320SpecificData;
}
/// <amd-module name="connectsdk.PaymentProduct863SpecificData" />
declare module "connectsdk.PaymentProduct863SpecificData" {
    import { PaymentProduct863SpecificDataJSON } from "connectsdk.apimodel";
    class PaymentProduct863SpecificData {
        readonly json: PaymentProduct863SpecificDataJSON;
        readonly integrationTypes: string[];
        constructor(json: PaymentProduct863SpecificDataJSON);
    }
    export = PaymentProduct863SpecificData;
}
/// <amd-module name="connectsdk.PaymentProductDisplayHints" />
declare module "connectsdk.PaymentProductDisplayHints" {
    import { PaymentProductDisplayHintsJSON } from "connectsdk.apimodel";
    class PaymentProductDisplayHints {
        readonly json: PaymentProductDisplayHintsJSON;
        readonly displayOrder: number;
        readonly label?: string;
        readonly logo: string;
        constructor(json: PaymentProductDisplayHintsJSON);
    }
    export = PaymentProductDisplayHints;
}
/// <amd-module name="connectsdk.BasicPaymentProduct" />
declare module "connectsdk.BasicPaymentProduct" {
    import AccountOnFile = require("connectsdk.AccountOnFile");
    import AuthenticationIndicator = require("connectsdk.AuthenticationIndicator");
    import PaymentProduct302SpecificData = require("connectsdk.PaymentProduct302SpecificData");
    import PaymentProduct320SpecificData = require("connectsdk.PaymentProduct320SpecificData");
    import PaymentProduct863SpecificData = require("connectsdk.PaymentProduct863SpecificData");
    import PaymentProductDisplayHints = require("connectsdk.PaymentProductDisplayHints");
    import { BasicPaymentProductJSON } from "connectsdk.apimodel";
    class BasicPaymentProduct {
        readonly json: BasicPaymentProductJSON;
        readonly accountsOnFile: AccountOnFile[];
        readonly accountOnFileById: {
            [id: number]: AccountOnFile | undefined;
        };
        readonly allowsRecurring: boolean;
        readonly allowsTokenization: boolean;
        readonly autoTokenized: boolean;
        readonly allowsInstallments: boolean;
        readonly authenticationIndicator?: AuthenticationIndicator;
        readonly acquirerCountry?: string;
        readonly canBeIframed?: boolean;
        readonly deviceFingerprintEnabled: boolean;
        readonly displayHints: PaymentProductDisplayHints;
        readonly id: number;
        readonly isJavaScriptRequired?: boolean;
        readonly maxAmount?: number;
        readonly minAmount?: number;
        readonly paymentMethod: string;
        readonly mobileIntegrationLevel: string;
        readonly usesRedirectionTo3rdParty: boolean;
        readonly paymentProduct302SpecificData?: PaymentProduct302SpecificData;
        readonly paymentProduct320SpecificData?: PaymentProduct320SpecificData;
        readonly paymentProduct863SpecificData?: PaymentProduct863SpecificData;
        readonly paymentProductGroup?: string;
        readonly supportsMandates?: boolean;
        readonly type = "product";
        constructor(json: BasicPaymentProductJSON);
        copy(): BasicPaymentProduct;
    }
    export = BasicPaymentProduct;
}
/// <amd-module name="connectsdk.BasicPaymentProductGroup" />
declare module "connectsdk.BasicPaymentProductGroup" {
    import AccountOnFile = require("connectsdk.AccountOnFile");
    import { BasicPaymentProductGroupJSON } from "connectsdk.apimodel";
    import PaymentProductDisplayHints = require("connectsdk.PaymentProductDisplayHints");
    class BasicPaymentProductGroup {
        readonly json: BasicPaymentProductGroupJSON;
        readonly id: string;
        readonly acquirerCountry?: string;
        readonly allowsInstallments: boolean;
        readonly displayHints: PaymentProductDisplayHints;
        readonly accountsOnFile: AccountOnFile[];
        readonly accountOnFileById: {
            [id: number]: AccountOnFile | undefined;
        };
        readonly type = "group";
        constructor(json: BasicPaymentProductGroupJSON);
        copy(): BasicPaymentProductGroup;
    }
    export = BasicPaymentProductGroup;
}
/// <amd-module name="connectsdk.BasicPaymentItem" />
declare module "connectsdk.BasicPaymentItem" {
    import BasicPaymentProduct = require("connectsdk.BasicPaymentProduct");
    import BasicPaymentProductGroup = require("connectsdk.BasicPaymentProductGroup");
    export type BasicPaymentItem = BasicPaymentProduct | BasicPaymentProductGroup;
    export type BasicPaymentItemByIdMap = {
        [id: number]: BasicPaymentItem;
        [id: string]: BasicPaymentItem;
    };
}
/// <amd-module name="connectsdk.BasicPaymentProductGroups" />
declare module "connectsdk.BasicPaymentProductGroups" {
    import AccountOnFile = require("connectsdk.AccountOnFile");
    import { PaymentProductGroupsJSON } from "connectsdk.apimodel";
    import BasicPaymentProductGroup = require("connectsdk.BasicPaymentProductGroup");
    class BasicPaymentProductGroups {
        readonly json: PaymentProductGroupsJSON;
        readonly basicPaymentProductGroups: BasicPaymentProductGroup[];
        readonly basicPaymentProductGroupById: {
            [id: string]: BasicPaymentProductGroup | undefined;
        };
        readonly accountsOnFile: AccountOnFile[];
        readonly accountOnFileById: {
            [id: number]: AccountOnFile | undefined;
        };
        constructor(json: PaymentProductGroupsJSON);
    }
    export = BasicPaymentProductGroups;
}
/// <amd-module name="connectsdk.BasicPaymentProducts" />
declare module "connectsdk.BasicPaymentProducts" {
    import AccountOnFile = require("connectsdk.AccountOnFile");
    import { PaymentProductsJSON } from "connectsdk.apimodel";
    import BasicPaymentProduct = require("connectsdk.BasicPaymentProduct");
    class BasicPaymentProducts {
        readonly json: PaymentProductsJSON;
        readonly basicPaymentProducts: BasicPaymentProduct[];
        readonly basicPaymentProductById: {
            [id: number]: BasicPaymentProduct | undefined;
        };
        readonly basicPaymentProductByAccountOnFileId: {
            [id: number]: BasicPaymentProduct | undefined;
        };
        readonly accountsOnFile: AccountOnFile[];
        readonly accountOnFileById: {
            [id: number]: AccountOnFile | undefined;
        };
        constructor(json: PaymentProductsJSON);
    }
    export = BasicPaymentProducts;
}
/// <amd-module name="connectsdk.BasicPaymentItems" />
declare module "connectsdk.BasicPaymentItems" {
    import AccountOnFile = require("connectsdk.AccountOnFile");
    import BasicPaymentProductGroups = require("connectsdk.BasicPaymentProductGroups");
    import BasicPaymentProducts = require("connectsdk.BasicPaymentProducts");
    import { BasicPaymentItem, BasicPaymentItemByIdMap } from "connectsdk.BasicPaymentItem";
    class BasicPaymentItems {
        readonly basicPaymentItems: BasicPaymentItem[];
        readonly basicPaymentItemById: BasicPaymentItemByIdMap;
        readonly accountsOnFile: AccountOnFile[];
        readonly accountOnFileById: {
            [id: number]: AccountOnFile | undefined;
        };
        constructor(products: BasicPaymentProducts, groups?: BasicPaymentProductGroups | null);
    }
    export = BasicPaymentItems;
}
/// <amd-module name="connectsdk.C2SCommunicatorConfiguration" />
declare module "connectsdk.C2SCommunicatorConfiguration" {
    import { SessionDetails } from "connectsdk.types";
    class C2SCommunicatorConfiguration {
        readonly clientSessionId: string;
        readonly customerId: string;
        readonly clientApiUrl: string;
        readonly assetUrl: string;
        constructor(sessionDetails: SessionDetails, apiVersion: string);
    }
    export = C2SCommunicatorConfiguration;
}
/// <amd-module name="connectsdk.C2SPaymentProductContext" />
declare module "connectsdk.C2SPaymentProductContext" {
    import { PaymentDetails } from "connectsdk.types";
    class C2SPaymentProductContext {
        readonly totalAmount: number;
        readonly countryCode: string;
        readonly isRecurring: boolean;
        readonly currency: string;
        readonly locale?: string;
        readonly isInstallments: boolean;
        readonly accountOnFileId?: number;
        readonly environment?: string | "PROD";
        constructor(payload: PaymentDetails);
    }
    export = C2SPaymentProductContext;
}
/// <amd-module name="connectsdk.GooglePay" />
declare module "connectsdk.GooglePay" {
    import C2SPaymentProductContext = require("connectsdk.C2SPaymentProductContext");
    import Promise = require("connectsdk.promise");
    import { PaymentProduct320SpecificDataJSON } from "connectsdk.apimodel";
    import { PaymentProductSpecificInputs } from "connectsdk.types";
    class GooglePay {
        readonly isGooglePayAvailable: (context: C2SPaymentProductContext, paymentProductSpecificInputs: PaymentProductSpecificInputs, googlePayData: PaymentProduct320SpecificDataJSON) => Promise<boolean>;
        readonly isMerchantIdProvided: (paymentProductSpecificInputs?: PaymentProductSpecificInputs) => boolean;
        constructor();
    }
    export = GooglePay;
}
/// <amd-module name="connectsdk.IinDetailsResponse" />
declare module "connectsdk.IinDetailsResponse" {
    import { ErrorResponseJSON, GetIINDetailsResponseJSON, IinDetailJSON } from "connectsdk.apimodel";
    import { IinDetailsStatus } from "connectsdk.types";
    class IinDetailsResponse {
        readonly status: IinDetailsStatus;
        readonly json?: GetIINDetailsResponseJSON | ErrorResponseJSON | undefined;
        readonly countryCode?: string;
        readonly paymentProductId?: number;
        readonly isAllowedInContext?: boolean;
        readonly coBrands?: IinDetailJSON[];
        constructor(status: IinDetailsStatus, json?: GetIINDetailsResponseJSON | ErrorResponseJSON | undefined);
    }
    export = IinDetailsResponse;
}
/// <amd-module name="connectsdk.net" />
declare module "connectsdk.net" {
    import { AjaxRequest, AjaxRequestOptions, JSONPOptions } from "connectsdk.types";
    class Net {
        /**
         * Serialize JavaScript for HTTP requests.
         *
         * @param {Object} object An Array or Object
         * @returns {String} A string suitable for a GET or POST request
         */
        static serialize(object: object | string): string;
        /**
         * Parses JSON represented as a string.
         *
         * @param {String} string The original string
         * @returns {Object} A JavaScript object
         */
        static parseJSON(string: string): unknown;
        /**
         * Parses XML represented as a string.
         *
         * @param {String} string The original string
         * @returns {Object} A JavaScript object
         */
        static parseXML(text: string): unknown;
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
        static ajax(url: string, options?: AjaxRequestOptions): AjaxRequest;
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
        static get(url: string, options?: AjaxRequestOptions): AjaxRequest;
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
        static post(url: string, options?: AjaxRequestOptions): AjaxRequest;
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
        static jsonp(url: string, options: JSONPOptions): void;
    }
    export = Net;
}
/// <amd-module name="connectsdk.PublicKeyResponse" />
declare module "connectsdk.PublicKeyResponse" {
    import { PublicKeyJSON } from "connectsdk.apimodel";
    class PublicKeyResponse {
        readonly json: PublicKeyJSON;
        readonly keyId: string;
        readonly publicKey: string;
        constructor(json: PublicKeyJSON);
    }
    export = PublicKeyResponse;
}
/// <amd-module name="connectsdk.C2SCommunicator" />
declare module "connectsdk.C2SCommunicator" {
    import C2SCommunicatorConfiguration = require("connectsdk.C2SCommunicatorConfiguration");
    import C2SPaymentProductContext = require("connectsdk.C2SPaymentProductContext");
    import IinDetailsResponse = require("connectsdk.IinDetailsResponse");
    import Promise = require("connectsdk.promise");
    import PublicKeyResponse = require("connectsdk.PublicKeyResponse");
    import { ConvertAmountJSON, CreatePaymentProductSessionResponseJSON, DirectoryJSON, GetCustomerDetailsRequestJSON, GetCustomerDetailsResponseJSON, GetIINDetailsRequestJSON, PaymentProductGroupJSON, PaymentProductGroupsJSON, PaymentProductJSON, PaymentProductNetworksResponseJSON, PaymentProductsJSON, ThirdPartyStatusResponseJSON } from "connectsdk.apimodel";
    import { ApplePayInitResult, ApplePaySpecificInput, PaymentDetails, PaymentProductSessionContext, PaymentProductSpecificInputs } from "connectsdk.types";
    class C2SCommunicator {
        readonly getBasicPaymentProducts: (context: C2SPaymentProductContext, paymentProductSpecificInputs?: PaymentProductSpecificInputs) => Promise<PaymentProductsJSON>;
        readonly getBasicPaymentProductGroups: (context: C2SPaymentProductContext) => Promise<PaymentProductGroupsJSON>;
        readonly getPaymentProduct: (paymentProductId: number, context: C2SPaymentProductContext, paymentProductSpecificInputs?: PaymentProductSpecificInputs) => Promise<PaymentProductJSON>;
        readonly getPaymentProductGroup: (paymentProductGroupId: string, context: C2SPaymentProductContext) => Promise<PaymentProductGroupJSON>;
        readonly getPaymentProductIdByCreditCardNumber: (partialCreditCardNumber: string, context: C2SPaymentProductContext) => Promise<IinDetailsResponse>;
        readonly convertContextToIinDetailsContext: (partialCreditCardNumber: string, context: C2SPaymentProductContext) => GetIINDetailsRequestJSON;
        readonly getPublicKey: () => Promise<PublicKeyResponse>;
        readonly getPaymentProductNetworks: (paymentProductId: number, context: C2SPaymentProductContext) => Promise<PaymentProductNetworksResponseJSON>;
        readonly getPaymentProductDirectory: (paymentProductId: number, currencyCode: string, countryCode: string) => Promise<DirectoryJSON>;
        readonly convertAmount: (amount: number, source: string, target: string) => Promise<ConvertAmountJSON>;
        readonly getThirdPartyPaymentStatus: (paymentId: string) => Promise<ThirdPartyStatusResponseJSON>;
        readonly getCustomerDetails: (paymentProductId: number, context: GetCustomerDetailsRequestJSON) => Promise<GetCustomerDetailsResponseJSON>;
        readonly createPaymentProductSession: (paymentProductId: number, context: PaymentProductSessionContext) => Promise<CreatePaymentProductSessionResponseJSON>;
        readonly initApplePayPayment: (context: PaymentDetails, paymentProductSpecificInput: ApplePaySpecificInput, networks: string[]) => Promise<ApplePayInitResult>;
        readonly transformPaymentProductJSON: <T extends PaymentProductJSON | PaymentProductGroupJSON>(json: T) => T;
        constructor(c2SCommunicatorConfiguration: C2SCommunicatorConfiguration, paymentProduct?: PaymentProductJSON | PaymentProductGroupJSON);
    }
    export = C2SCommunicator;
}
/// <amd-module name="connectsdk.ValidationRuleBoletoBancarioRequiredness" />
declare module "connectsdk.ValidationRuleBoletoBancarioRequiredness" {
    import { BoletoBancarioRequirednessValidatorJSON } from "connectsdk.apimodel";
    import { ValidatableRequest, ValidationRule, ValidationRuleDefinition } from "connectsdk.types";
    class ValidationRuleBoletoBancarioRequiredness implements ValidationRule {
        readonly json: ValidationRuleDefinition<BoletoBancarioRequirednessValidatorJSON>;
        readonly type: string;
        readonly errorMessageId: string;
        readonly fiscalNumberLength: number;
        constructor(json: ValidationRuleDefinition<BoletoBancarioRequirednessValidatorJSON>);
        validate(value: string, fiscalNumberValue?: string): boolean;
        validateValue(request: ValidatableRequest, fieldId: string): boolean;
    }
    export = ValidationRuleBoletoBancarioRequiredness;
}
/// <amd-module name="connectsdk.ValidationRuleEmailAddress" />
declare module "connectsdk.ValidationRuleEmailAddress" {
    import { EmptyValidatorJSON } from "connectsdk.apimodel";
    import { ValidatableRequest, ValidationRule, ValidationRuleDefinition } from "connectsdk.types";
    class ValidationRuleEmailAddress implements ValidationRule {
        readonly json: ValidationRuleDefinition<EmptyValidatorJSON>;
        readonly type: string;
        readonly errorMessageId: string;
        constructor(json: ValidationRuleDefinition<EmptyValidatorJSON>);
        validate(value: string): boolean;
        validateValue(request: ValidatableRequest, fieldId: string): boolean;
    }
    export = ValidationRuleEmailAddress;
}
/// <amd-module name="connectsdk.ValidationRuleExpirationDate" />
declare module "connectsdk.ValidationRuleExpirationDate" {
    import { EmptyValidatorJSON } from "connectsdk.apimodel";
    import { ValidatableRequest, ValidationRule, ValidationRuleDefinition } from "connectsdk.types";
    class ValidationRuleExpirationDate implements ValidationRule {
        readonly json: ValidationRuleDefinition<EmptyValidatorJSON>;
        readonly type: string;
        readonly errorMessageId: string;
        constructor(json: ValidationRuleDefinition<EmptyValidatorJSON>);
        validate(value: string): boolean;
        validateValue(request: ValidatableRequest, fieldId: string): boolean;
    }
    export = ValidationRuleExpirationDate;
}
/// <amd-module name="connectsdk.ValidationRuleFixedList" />
declare module "connectsdk.ValidationRuleFixedList" {
    import { FixedListValidatorJSON } from "connectsdk.apimodel";
    import { ValidatableRequest, ValidationRule, ValidationRuleDefinition } from "connectsdk.types";
    class ValidationRuleFixedList implements ValidationRule {
        readonly json: ValidationRuleDefinition<FixedListValidatorJSON>;
        readonly type: string;
        readonly errorMessageId: string;
        readonly allowedValues: string[];
        constructor(json: ValidationRuleDefinition<FixedListValidatorJSON>);
        validate(value: string): boolean;
        validateValue(request: ValidatableRequest, fieldId: string): boolean;
    }
    export = ValidationRuleFixedList;
}
/// <amd-module name="connectsdk.ValidationRuleIban" />
declare module "connectsdk.ValidationRuleIban" {
    import { EmptyValidatorJSON } from "connectsdk.apimodel";
    import { ValidatableRequest, ValidationRule, ValidationRuleDefinition } from "connectsdk.types";
    /**
     * Validate Iban by given json
     */
    class ValidationRuleIban implements ValidationRule {
        readonly json: ValidationRuleDefinition<EmptyValidatorJSON>;
        readonly type: string;
        readonly errorMessageId: string;
        constructor(json: ValidationRuleDefinition<EmptyValidatorJSON>);
        /**
         * Validate Iban nrule
         *
         * @see https://github.com/arhs/iban.js/blob/master/iban.js
         */
        validate(value: string): boolean;
        validateValue(request: ValidatableRequest, fieldId: string): boolean;
    }
    export = ValidationRuleIban;
}
/// <amd-module name="connectsdk.ValidationRuleLength" />
declare module "connectsdk.ValidationRuleLength" {
    import { LengthValidatorJSON } from "connectsdk.apimodel";
    import { ValidatableRequest, ValidationRule, ValidationRuleDefinition } from "connectsdk.types";
    class ValidationRuleLength implements ValidationRule {
        readonly json: ValidationRuleDefinition<LengthValidatorJSON>;
        readonly type: string;
        readonly errorMessageId: string;
        readonly minLength: number;
        readonly maxLength: number;
        constructor(json: ValidationRuleDefinition<LengthValidatorJSON>);
        validate(value: string): boolean;
        validateValue(request: ValidatableRequest, fieldId: string): boolean;
    }
    export = ValidationRuleLength;
}
/// <amd-module name="connectsdk.ValidationRuleLuhn" />
declare module "connectsdk.ValidationRuleLuhn" {
    import { EmptyValidatorJSON } from "connectsdk.apimodel";
    import { ValidatableRequest, ValidationRule, ValidationRuleDefinition } from "connectsdk.types";
    class ValidationRuleLuhn implements ValidationRule {
        readonly json: ValidationRuleDefinition<EmptyValidatorJSON>;
        readonly type: string;
        readonly errorMessageId: string;
        constructor(json: ValidationRuleDefinition<EmptyValidatorJSON>);
        validate(value: string): boolean;
        validateValue(request: ValidatableRequest, fieldId: string): boolean;
    }
    export = ValidationRuleLuhn;
}
/// <amd-module name="connectsdk.ValidationRuleRange" />
declare module "connectsdk.ValidationRuleRange" {
    import { RangeValidatorJSON } from "connectsdk.apimodel";
    import { ValidatableRequest, ValidationRule, ValidationRuleDefinition } from "connectsdk.types";
    class ValidationRuleRange implements ValidationRule {
        readonly json: ValidationRuleDefinition<RangeValidatorJSON>;
        readonly type: string;
        readonly errorMessageId: string;
        readonly minValue: number;
        readonly maxValue: number;
        constructor(json: ValidationRuleDefinition<RangeValidatorJSON>);
        validate(value: string | number): boolean;
        validateValue(request: ValidatableRequest, fieldId: string): boolean;
    }
    export = ValidationRuleRange;
}
/// <amd-module name="connectsdk.ValidationRuleRegularExpression" />
declare module "connectsdk.ValidationRuleRegularExpression" {
    import { RegularExpressionValidatorJSON } from "connectsdk.apimodel";
    import { ValidatableRequest, ValidationRule, ValidationRuleDefinition } from "connectsdk.types";
    class ValidationRuleRegularExpression implements ValidationRule {
        readonly json: ValidationRuleDefinition<RegularExpressionValidatorJSON>;
        readonly type: string;
        readonly errorMessageId: string;
        readonly regularExpression: string;
        constructor(json: ValidationRuleDefinition<RegularExpressionValidatorJSON>);
        validate(value: string): boolean;
        validateValue(request: ValidatableRequest, fieldId: string): boolean;
    }
    export = ValidationRuleRegularExpression;
}
/// <amd-module name="connectsdk.ValidationRuleResidentIdNumber" />
declare module "connectsdk.ValidationRuleResidentIdNumber" {
    import { EmptyValidatorJSON } from "connectsdk.apimodel";
    import { ValidatableRequest, ValidationRule, ValidationRuleDefinition } from "connectsdk.types";
    class ValidationRuleResidentIdNumber implements ValidationRule {
        readonly json: ValidationRuleDefinition<EmptyValidatorJSON>;
        readonly type: string;
        readonly errorMessageId: string;
        constructor(json: ValidationRuleDefinition<EmptyValidatorJSON>);
        validate(value: string): boolean;
        validateValue(request: ValidatableRequest, fieldId: string): boolean;
    }
    export = ValidationRuleResidentIdNumber;
}
/// <amd-module name="connectsdk.ValidationRuleTermsAndConditions" />
declare module "connectsdk.ValidationRuleTermsAndConditions" {
    import { EmptyValidatorJSON } from "connectsdk.apimodel";
    import { ValidatableRequest, ValidationRule, ValidationRuleDefinition } from "connectsdk.types";
    class ValidationRuleTermsAndConditions implements ValidationRule {
        readonly json: ValidationRuleDefinition<EmptyValidatorJSON>;
        readonly type: string;
        readonly errorMessageId: string;
        constructor(json: ValidationRuleDefinition<EmptyValidatorJSON>);
        validate(value: string | boolean): boolean;
        validateValue(request: ValidatableRequest, fieldId: string): boolean;
    }
    export = ValidationRuleTermsAndConditions;
}
/// <amd-module name="connectsdk.ValidationRuleFactory" />
declare module "connectsdk.ValidationRuleFactory" {
    import { ValidationRule, ValidationRuleDefinition } from "connectsdk.types";
    class ValidationRuleFactory {
        makeValidator(json: ValidationRuleDefinition<unknown>): ValidationRule | null;
    }
    export = ValidationRuleFactory;
}
/// <amd-module name="connectsdk.DataRestrictions" />
declare module "connectsdk.DataRestrictions" {
    import { PaymentProductFieldDataRestrictionsJSON } from "connectsdk.apimodel";
    import { ValidationRule } from "connectsdk.types";
    class DataRestrictions {
        readonly json: PaymentProductFieldDataRestrictionsJSON;
        readonly isRequired: boolean;
        readonly validationRules: ValidationRule[];
        readonly validationRuleByType: {
            [type: string]: ValidationRule | undefined;
        };
        constructor(json: PaymentProductFieldDataRestrictionsJSON);
    }
    export = DataRestrictions;
}
/// <amd-module name="connectsdk.JOSEEncryptor" />
declare module "connectsdk.JOSEEncryptor" {
    import PublicKeyResponse = require("connectsdk.PublicKeyResponse");
    class JOSEEncryptor {
        encrypt(plainTextValues: any, publicKeyResponse: PublicKeyResponse): string;
    }
    export = JOSEEncryptor;
}
/// <amd-module name="connectsdk.ValueMappingElement" />
declare module "connectsdk.ValueMappingElement" {
    import { ValueMappingElementJSON } from "connectsdk.apimodel";
    class ValueMappingElement {
        readonly json: ValueMappingElementJSON;
        readonly displayName?: string;
        readonly value: string;
        constructor(json: ValueMappingElementJSON);
    }
    export = ValueMappingElement;
}
/// <amd-module name="connectsdk.FormElement" />
declare module "connectsdk.FormElement" {
    import { PaymentProductFieldFormElementJSON } from "connectsdk.apimodel";
    import ValueMappingElement = require("connectsdk.ValueMappingElement");
    class FormElement {
        readonly json: PaymentProductFieldFormElementJSON;
        readonly type: string;
        readonly valueMapping: ValueMappingElement[];
        constructor(json: PaymentProductFieldFormElementJSON);
    }
    export = FormElement;
}
/// <amd-module name="connectsdk.Tooltip" />
declare module "connectsdk.Tooltip" {
    import { PaymentProductFieldTooltipJSON } from "connectsdk.apimodel";
    class Tooltip {
        readonly json: PaymentProductFieldTooltipJSON;
        readonly image: string;
        readonly label?: string;
        constructor(json: PaymentProductFieldTooltipJSON);
    }
    export = Tooltip;
}
/// <amd-module name="connectsdk.PaymentProductFieldDisplayHints" />
declare module "connectsdk.PaymentProductFieldDisplayHints" {
    import { PaymentProductFieldDisplayHintsJSON } from "connectsdk.apimodel";
    import FormElement = require("connectsdk.FormElement");
    import Tooltip = require("connectsdk.Tooltip");
    class PaymentProductFieldDisplayHints {
        readonly json: PaymentProductFieldDisplayHintsJSON;
        readonly displayOrder: number;
        readonly formElement?: FormElement;
        readonly label?: string;
        readonly mask?: string;
        readonly obfuscate: boolean;
        readonly placeholderLabel?: string;
        readonly preferredInputType?: string;
        readonly tooltip?: Tooltip;
        readonly alwaysShow: boolean;
        readonly wildcardMask: string;
        constructor(json: PaymentProductFieldDisplayHintsJSON);
    }
    export = PaymentProductFieldDisplayHints;
}
/// <amd-module name="connectsdk.PaymentProductField" />
declare module "connectsdk.PaymentProductField" {
    import { PaymentProductFieldJSON } from "connectsdk.apimodel";
    import DataRestrictions = require("connectsdk.DataRestrictions");
    import MaskedString = require("connectsdk.MaskedString");
    import PaymentProductFieldDisplayHints = require("connectsdk.PaymentProductFieldDisplayHints");
    import { ValidatableRequest } from "connectsdk.types";
    class PaymentProductField {
        readonly json: PaymentProductFieldJSON;
        readonly displayHints: PaymentProductFieldDisplayHints | "";
        readonly dataRestrictions: DataRestrictions;
        readonly id: string;
        readonly type: string;
        /**
         * @deprecated This function does not take into account other fields that may be of importance for the validation.
         *             Use {@link .getErrorMessageIds} instead.
         */
        readonly getErrorCodes: (value?: string) => string[];
        readonly getErrorMessageIds: (request?: ValidatableRequest) => string[];
        /**
         * @deprecated This function does not take into account other fields that may be of importance for the validation.
         *             Use {@link .validateValue} instead.
         */
        readonly isValid: (value: string) => boolean;
        readonly validateValue: (request: ValidatableRequest) => boolean;
        constructor(json: PaymentProductFieldJSON);
        applyMask(newValue: string, oldValue?: string): MaskedString;
        applyWildcardMask(newValue: string, oldValue?: string): MaskedString;
        removeMask(value: string): string;
    }
    export = PaymentProductField;
}
/// <amd-module name="connectsdk.PaymentProduct" />
declare module "connectsdk.PaymentProduct" {
    import { PaymentProductJSON } from "connectsdk.apimodel";
    import BasicPaymentProduct = require("connectsdk.BasicPaymentProduct");
    import PaymentProductField = require("connectsdk.PaymentProductField");
    class PaymentProduct extends BasicPaymentProduct {
        readonly json: PaymentProductJSON;
        readonly paymentProductFields: PaymentProductField[];
        readonly paymentProductFieldById: {
            [id: string]: PaymentProductField | undefined;
        };
        readonly fieldsWarning?: string;
        constructor(json: PaymentProductJSON);
    }
    export = PaymentProduct;
}
/// <amd-module name="connectsdk.PaymentRequest" />
declare module "connectsdk.PaymentRequest" {
    import AccountOnFile = require("connectsdk.AccountOnFile");
    import PaymentProduct = require("connectsdk.PaymentProduct");
    import { ValidationError } from "connectsdk.types";
    class PaymentRequest {
        readonly setValue: (paymentProductFieldId: string, value: string) => void;
        readonly setTokenize: (tokenize: boolean) => void;
        readonly getTokenize: () => boolean;
        /**
         * @deprecated This function does not return for which field the errors are. Use {@link .validate} instead.
         */
        readonly getErrorMessageIds: () => string[];
        readonly getValue: (paymentProductFieldId: string) => string | undefined;
        readonly getValues: () => {
            [id: string]: string | undefined;
        };
        readonly getMaskedValue: (paymentProductFieldId: string) => string | undefined;
        readonly getMaskedValues: () => {
            [id: string]: string | undefined;
        };
        readonly getUnmaskedValue: (paymentProductFieldId: string) => string | undefined;
        readonly getUnmaskedValues: () => {
            [id: string]: string | undefined;
        };
        readonly setPaymentProduct: (paymentProduct: PaymentProduct) => void;
        readonly getPaymentProduct: () => PaymentProduct | undefined;
        readonly setAccountOnFile: (accountOnFile?: AccountOnFile | null) => void;
        readonly getAccountOnFile: () => AccountOnFile | undefined;
        readonly getClientSessionID: () => string;
        constructor(clientSessionID: string);
        isValid(): boolean;
        /**
         * Validates that the necessary fields are set with correct values.
         * @throws If the payment product has not been set yet.
         */
        validate(): ValidationError[];
    }
    export = PaymentRequest;
}
/// <amd-module name="connectsdk.Encryptor" />
declare module "connectsdk.Encryptor" {
    import PaymentRequest = require("connectsdk.PaymentRequest");
    import Promise = require("connectsdk.promise");
    import PublicKeyResponse = require("connectsdk.PublicKeyResponse");
    class Encryptor {
        /**
         * Encrypts the given payment request. Calls {@link PaymentRequest.validate}, so it's not necessary to do that first.
         * If validation fails, the returned promise is rejected with the validation errors.
         */
        readonly encrypt: (paymentRequest: PaymentRequest) => Promise<string>;
        constructor(publicKeyResponsePromise: Promise<PublicKeyResponse>);
    }
    export = Encryptor;
}
/// <amd-module name="connectsdk.PaymentProductGroup" />
declare module "connectsdk.PaymentProductGroup" {
    import { PaymentProductGroupJSON } from "connectsdk.apimodel";
    import BasicPaymentProductGroup = require("connectsdk.BasicPaymentProductGroup");
    import PaymentProductField = require("connectsdk.PaymentProductField");
    class PaymentProductGroup extends BasicPaymentProductGroup {
        readonly json: PaymentProductGroupJSON;
        readonly paymentProductFields: PaymentProductField[];
        readonly paymentProductFieldById: {
            [id: string]: PaymentProductField | undefined;
        };
        constructor(json: PaymentProductGroupJSON);
    }
    export = PaymentProductGroup;
}
/// <amd-module name="connectsdk.PaymentItem" />
declare module "connectsdk.PaymentItem" {
    import PaymentProduct = require("connectsdk.PaymentProduct");
    import PaymentProductGroup = require("connectsdk.PaymentProductGroup");
    export type PaymentItem = PaymentProduct | PaymentProductGroup;
}
/// <amd-module name="connectsdk.Session" />
declare module "connectsdk.Session" {
    import BasicPaymentItems = require("connectsdk.BasicPaymentItems");
    import BasicPaymentProductGroups = require("connectsdk.BasicPaymentProductGroups");
    import BasicPaymentProducts = require("connectsdk.BasicPaymentProducts");
    import Encryptor = require("connectsdk.Encryptor");
    import IinDetailsResponse = require("connectsdk.IinDetailsResponse");
    import PaymentProduct = require("connectsdk.PaymentProduct");
    import PaymentProductGroup = require("connectsdk.PaymentProductGroup");
    import PaymentRequest = require("connectsdk.PaymentRequest");
    import Promise = require("connectsdk.promise");
    import PublicKeyResponse = require("connectsdk.PublicKeyResponse");
    import { ConvertAmountJSON, CreatePaymentProductSessionResponseJSON, DirectoryJSON, GetCustomerDetailsRequestJSON, GetCustomerDetailsResponseJSON, PaymentProductGroupJSON, PaymentProductJSON, PaymentProductNetworksResponseJSON, ThirdPartyStatusResponseJSON } from "connectsdk.apimodel";
    import { ApplePayInitResult, ApplePaySpecificInput, PaymentDetails, PaymentProductSessionContext, PaymentProductSpecificInputs, SessionDetails } from "connectsdk.types";
    class session {
        readonly clientApiUrl: string;
        readonly assetUrl: string;
        readonly getBasicPaymentProducts: (paymentDetails: PaymentDetails, paymentProductSpecificInputs?: PaymentProductSpecificInputs) => Promise<BasicPaymentProducts>;
        readonly getBasicPaymentProductGroups: (paymentDetails: PaymentDetails) => Promise<BasicPaymentProductGroups>;
        readonly getBasicPaymentItems: (paymentDetails: PaymentDetails, useGroups: boolean, paymentProductSpecificInputs?: PaymentProductSpecificInputs) => Promise<BasicPaymentItems>;
        readonly getPaymentProduct: (paymentProductId: number, paymentDetails?: PaymentDetails, paymentProductSpecificInputs?: PaymentProductSpecificInputs) => Promise<PaymentProduct>;
        readonly getPaymentProductGroup: (paymentProductGroupId: string, paymentDetails?: PaymentDetails) => Promise<PaymentProductGroup>;
        readonly getIinDetails: (partialCreditCardNumber: string, paymentDetails?: PaymentDetails | null) => Promise<IinDetailsResponse>;
        readonly getPublicKey: () => Promise<PublicKeyResponse>;
        readonly getPaymentProductNetworks: (paymentProductId: number, paymentDetails: PaymentDetails) => Promise<PaymentProductNetworksResponseJSON>;
        readonly getPaymentProductDirectory: (paymentProductId: number, currencyCode: string, countryCode: string) => Promise<DirectoryJSON>;
        readonly convertAmount: (amount: number, source: string, target: string) => Promise<ConvertAmountJSON>;
        readonly getPaymentRequest: () => PaymentRequest;
        readonly getEncryptor: () => Encryptor;
        readonly getThirdPartyPaymentStatus: (paymentId: string) => Promise<ThirdPartyStatusResponseJSON>;
        readonly getCustomerDetails: (paymentProductId: number, paymentDetails: GetCustomerDetailsRequestJSON) => Promise<GetCustomerDetailsResponseJSON>;
        readonly createPaymentProductSession: (paymentProductId: number, paymentDetails: PaymentProductSessionContext) => Promise<CreatePaymentProductSessionResponseJSON>;
        readonly createApplePayPayment: (context: PaymentDetails, paymentProductSpecificInput: ApplePaySpecificInput, networks: string[]) => Promise<ApplePayInitResult>;
        readonly transformPaymentProductJSON: (json: PaymentProductJSON) => PaymentProduct;
        readonly transformPaymentProductGroupJSON: (json: PaymentProductGroupJSON) => PaymentProductGroup;
        constructor(sessionDetails: SessionDetails, paymentProduct?: PaymentProductJSON | PaymentProductGroupJSON);
    }
    export = session;
}
/// <amd-module name="connectsdk.core" />
declare module "connectsdk.core" {
    import _AccountOnFile = require("connectsdk.AccountOnFile");
    import _AccountOnFileDisplayHints = require("connectsdk.AccountOnFileDisplayHints");
    import _ApplePay = require("connectsdk.ApplePay");
    import _Attribute = require("connectsdk.Attribute");
    import _BasicPaymentItems = require("connectsdk.BasicPaymentItems");
    import _BasicPaymentProduct = require("connectsdk.BasicPaymentProduct");
    import _BasicPaymentProductGroup = require("connectsdk.BasicPaymentProductGroup");
    import _BasicPaymentProductGroups = require("connectsdk.BasicPaymentProductGroups");
    import _BasicPaymentProducts = require("connectsdk.BasicPaymentProducts");
    import _C2SCommunicator = require("connectsdk.C2SCommunicator");
    import _C2SCommunicatorConfiguration = require("connectsdk.C2SCommunicatorConfiguration");
    import _C2SPaymentProductContext = require("connectsdk.C2SPaymentProductContext");
    import _DataRestrictions = require("connectsdk.DataRestrictions");
    import _Encryptor = require("connectsdk.Encryptor");
    import _FormElement = require("connectsdk.FormElement");
    import _GooglePay = require("connectsdk.GooglePay");
    import _IinDetailsResponse = require("connectsdk.IinDetailsResponse");
    import _JOSEEncryptor = require("connectsdk.JOSEEncryptor");
    import _LabelTemplateElement = require("connectsdk.LabelTemplateElement");
    import _MaskedString = require("connectsdk.MaskedString");
    import _MaskingUtil = require("connectsdk.MaskingUtil");
    import _Net = require("connectsdk.net");
    import _PaymentProduct = require("connectsdk.PaymentProduct");
    import _PaymentProduct302SpecificData = require("connectsdk.PaymentProduct302SpecificData");
    import _PaymentProduct320SpecificData = require("connectsdk.PaymentProduct320SpecificData");
    import _PaymentProduct863SpecificData = require("connectsdk.PaymentProduct863SpecificData");
    import _PaymentProductField = require("connectsdk.PaymentProductField");
    import _PaymentProductFieldDisplayHints = require("connectsdk.PaymentProductFieldDisplayHints");
    import _PaymentProductGroup = require("connectsdk.PaymentProductGroup");
    import _PaymentRequest = require("connectsdk.PaymentRequest");
    import _Promise = require("connectsdk.promise");
    import _PublicKeyResponse = require("connectsdk.PublicKeyResponse");
    import _session = require("connectsdk.Session");
    import _Tooltip = require("connectsdk.Tooltip");
    import _ValidationRuleBoletoBancarioRequiredness = require("connectsdk.ValidationRuleBoletoBancarioRequiredness");
    import _ValidationRuleEmailAddress = require("connectsdk.ValidationRuleEmailAddress");
    import _ValidationRuleExpirationDate = require("connectsdk.ValidationRuleExpirationDate");
    import _ValidationRuleFactory = require("connectsdk.ValidationRuleFactory");
    import _ValidationRuleFixedList = require("connectsdk.ValidationRuleFixedList");
    import _ValidationRuleIban = require("connectsdk.ValidationRuleIban");
    import _ValidationRuleLength = require("connectsdk.ValidationRuleLength");
    import _ValidationRuleLuhn = require("connectsdk.ValidationRuleLuhn");
    import _ValidationRuleRange = require("connectsdk.ValidationRuleRange");
    import _ValidationRuleRegularExpression = require("connectsdk.ValidationRuleRegularExpression");
    import _ValidationRuleResidentIdNumber = require("connectsdk.ValidationRuleResidentIdNumber");
    import _ValidationRuleTermsAndConditions = require("connectsdk.ValidationRuleTermsAndConditions");
    import _ValueMappingElement = require("connectsdk.ValueMappingElement");
    const connectsdk: {
        AccountOnFile: typeof _AccountOnFile;
        AccountOnFileDisplayHints: typeof _AccountOnFileDisplayHints;
        ApplePay: typeof _ApplePay;
        Attribute: typeof _Attribute;
        BasicPaymentItems: typeof _BasicPaymentItems;
        BasicPaymentProduct: typeof _BasicPaymentProduct;
        BasicPaymentProductGroup: typeof _BasicPaymentProductGroup;
        BasicPaymentProductGroups: typeof _BasicPaymentProductGroups;
        BasicPaymentProducts: typeof _BasicPaymentProducts;
        C2SCommunicator: typeof _C2SCommunicator;
        C2SCommunicatorConfiguration: typeof _C2SCommunicatorConfiguration;
        C2SPaymentProductContext: typeof _C2SPaymentProductContext;
        DataRestrictions: typeof _DataRestrictions;
        Encryptor: typeof _Encryptor;
        FormElement: typeof _FormElement;
        GooglePay: typeof _GooglePay;
        IinDetailsResponse: typeof _IinDetailsResponse;
        JOSEEncryptor: typeof _JOSEEncryptor;
        LabelTemplateElement: typeof _LabelTemplateElement;
        MaskedString: typeof _MaskedString;
        MaskingUtil: typeof _MaskingUtil;
        net: typeof _Net;
        get: typeof _Net.get;
        post: typeof _Net.post;
        jsonp: typeof _Net.jsonp;
        PaymentProduct: typeof _PaymentProduct;
        PaymentProduct302SpecificData: typeof _PaymentProduct302SpecificData;
        PaymentProduct320SpecificData: typeof _PaymentProduct320SpecificData;
        PaymentProduct863SpecificData: typeof _PaymentProduct863SpecificData;
        PaymentProductField: typeof _PaymentProductField;
        PaymentProductFieldDisplayHints: typeof _PaymentProductFieldDisplayHints;
        PaymentProductGroup: typeof _PaymentProductGroup;
        PaymentRequest: typeof _PaymentRequest;
        Promise: typeof _Promise;
        PublicKeyResponse: typeof _PublicKeyResponse;
        Session: typeof _session;
        Tooltip: typeof _Tooltip;
        Util: {
            getInstance(): import("connectsdk.types").Util;
        };
        ValidationRuleBoletoBancarioRequiredness: typeof _ValidationRuleBoletoBancarioRequiredness;
        ValidationRuleEmailAddress: typeof _ValidationRuleEmailAddress;
        ValidationRuleExpirationDate: typeof _ValidationRuleExpirationDate;
        ValidationRuleFactory: typeof _ValidationRuleFactory;
        ValidationRuleFixedList: typeof _ValidationRuleFixedList;
        ValidationRuleIban: typeof _ValidationRuleIban;
        ValidationRuleLength: typeof _ValidationRuleLength;
        ValidationRuleLuhn: typeof _ValidationRuleLuhn;
        ValidationRuleRange: typeof _ValidationRuleRange;
        ValidationRuleRegularExpression: typeof _ValidationRuleRegularExpression;
        ValidationRuleResidentIdNumber: typeof _ValidationRuleResidentIdNumber;
        ValidationRuleTermsAndConditions: typeof _ValidationRuleTermsAndConditions;
        ValueMappingElement: typeof _ValueMappingElement;
    };
    export = connectsdk;
}
