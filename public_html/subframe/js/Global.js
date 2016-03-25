/* global objLauncher, boolIsPuppiesBlocked */

function subdebug(mulValue) {
    console.log(mulValue);
}
;

function getJsLibPath (strSubpath) {
    var strPath = '/'+objLauncher.strApplicationName+'/js/libs/'+strSubpath;
    return strPath;
    
};

function isClientBlockPuppies () {
    if (typeof boolIsPuppiesBlocked === 'undefined') {
        boolIsPuppiesBlocked = true;
    };
    return boolIsPuppiesBlocked;
}

function empty(mul) {

    var boolReturn = false, numKey = 0, strKey = "";

    if (mul === undefined) {
        boolReturn = true;
    } else if (mul === 0) {
        boolReturn = true;
    } else if (mul === "") {
        boolReturn = true;
    } else if (mul === null) {
        boolReturn = true;
    } else if (Object.prototype.toString.call(mul) === '[object Array]') {
        if (mul.length === 0) {
            boolReturn = true;
        }
    } else if (Object.prototype.toString.call(mul) === '[object Object]') {

        if (mul instanceof jQuery) {
            boolReturn = $.isEmptyObject(mul[0]);
        } else {

            for (strKey in mul) {
                if (mul.hasOwnProperty(strKey)) {
                    numKey += 1;
                }
            }
            if (numKey === 0) {
                boolReturn = true;
            }
        }
    }

    return boolReturn;
};

function gv(mul, strKey, mulDefault) {
    var mulValue = mulDefault;

    if (mul[strKey] !== undefined) {
        mulValue = mul[strKey];
    };

    return mulValue;
}
;

function randomString(numLength, strCharRange) {
    var strResult = '', numI = 0;

    if (empty(strCharRange)) {
        strCharRange = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    };
    if (empty(numLength)) {
        numLength = 20;
    };
    for (numI = numLength; numI > 0; numI -= 1) {
        strResult += strCharRange[Math.round(Math.random() * (strCharRange.length - 1))];
    };

    return strResult;
};

if (console === undefined) {
    var console = function () {
        this.debug = function (arg) {
            wewdebug(arg);
        };
        this.log = function (arg) {
            wewdebug(arg);
        };
    };
}
;
