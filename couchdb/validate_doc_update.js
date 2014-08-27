function (newDoc, oldDoc, userCtx, secObj) {
    function mandatory(doc,attribute) {
        if (!doc.hasOwnProperty(attribute)) {
            throw({forbidden:"missing "+attribute});
        }
    }
    function shouldBeString(doc,attribute) {
        if (doc.hasOwnProperty(attribute) && !isString(doc[attribute])) {
            throw({forbidden:attribute+" must be a string"});
        }
    }
    function shouldBeArray(doc,attribute,callbackTest) {
        if (doc.hasOwnProperty(attribute) && !isArray(doc[attribute])) {
            throw({forbidden:attribute+" must be an array"});
        }
    }
    function shouldBeObject(doc,attribute) {
        if (doc.hasOwnProperty(attribute) && !isObject(doc[attribute])) {
            throw({forbidden:attribute+" must be an object"});
        }
    }
    function isStringOrNull(val) {
        return isString(val) || isNull(val);
    }
    function isNull(val) {
        return val ===null;
    }
    function isString(val) {
        return typeof val ==="string" || typeof val === "number";
    }
    function isArray(val) {
        return (typeof val.forEach ==="function");
    }
    function isObject(val) {
        return typeof val === "object" && !isArray(val);
    }
    
    function mandatoryFields(fields,doc) {
        doc=doc || newDoc;
        fields.forEach(function(attribute) {
            mandatory(doc,attribute);
        });
    }
    function ensureStrings(fields,doc) {
        doc=doc || newDoc;
        fields.forEach(function(attribute) {
            shouldBeString(doc,attribute);
        });
    }
    function ensureObjects(fields,doc) {
        doc=doc || newDoc;        
        fields.forEach(function(attribute) {
            shouldBeObject(doc,attribute);
        });
    }
    function ensureArrays(fields,doc) {
        doc=doc || newDoc;
        fields.forEach(function(attribute) {
            shouldBeArray(doc,attribute);
        });
    }
    function testArray(array,callback) {
        var ok=false;
        if (isArray(array)) {
            ok=true;
            if (typeof callback=="function") {
                array.forEach(function(val) {
                    if (!callback(val)) {
                        ok=false;
                    }
                });
            }
        }
        return ok;
    }
    
    if (newDoc.hasOwnProperty("title")) {
        ensureArrays(["text"]);
        if (newDoc.text && !testArray(newDoc.text,isString)) {
            throw({forbidden:"text lines must be strings "+JSON.stringify(newDoc.text)});
        }
        mandatoryFields(["translations"]);
        ensureStrings(["creator","date","language","title"]);
        ensureObjects(["translations"]);
        for (var t in newDoc.translations) {
            var translation=newDoc.translations[t];
            mandatory(translation,"text");
            shouldBeArray(translation,"text");
        }
    }
}
