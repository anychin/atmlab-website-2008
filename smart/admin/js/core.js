(function() {
 
  var _toString = Object.prototype.toString;
 
  function extend(destination, source) {
    for (var property in source)
      destination[property] = source[property];
    return destination;
  }
 
  function toJSON(object) {
    var type = typeof object;
    switch (type) {
      case 'undefined':
      case 'function':
      case 'unknown': return;
      case 'boolean': return object.toString();
    }
 
    if (object === null) return 'null';
    if (object.toJSON) return object.toJSON();
    if (isElement(object)) return;
 
    var results = [];
    for (var property in object) {
      var value = toJSON(object[property]);
      if (!isUndefined(value))
        results.push(property.toJSON() + ': ' + value);
    }
 
    return '{' + results.join(', ') + '}';
  }
 
  function keys(object) {
    var results = [];
    for (var property in object)
      results.push(property);
    return results;
  }
 
  function values(object) {
    var results = [];
    for (var property in object)
      results.push(object[property]);
    return results;
  }
 
  function clone(object) {
    return extend({ }, object);
  }
 
  function isElement(object) {
    return !!(object && object.nodeType == 1);
  }
 
  function isArray(object) {
    return _toString.call(object) == "[object Array]";
  }
 
 
  function isFunction(object) {
    return typeof object === "function";
  }
 
  function isString(object) {
    return _toString.call(object) == "[object String]";
  }
 
  function isNumber(object) {
    return _toString.call(object) == "[object Number]";
  }
 
 function isUndefined(object) {
    return typeof object === "undefined";
  }
 
  extend(Object, {
    extend:        extend,
    toJSON:        toJSON,
    keys:          keys,
    values:        values,
    clone:         clone,
    isElement:     isElement,
    isArray:       isArray,
    isFunction:    isFunction,
    isString:      isString,
    isNumber:      isNumber,
    isUndefined:   isUndefined
  });
})();

var Class = (function() {
  function subclass() {};
  function create() {
    var parent = null, properties = $A(arguments);
    if (Object.isFunction(properties[0]))
      parent = properties.shift();
 
    function klass() {
      this.initialize.apply(this, arguments);
    }
 
    Object.extend(klass, Class.Methods);
    klass.superclass = parent;
    klass.subclasses = [];
 
    if (parent) {
      subclass.prototype = parent.prototype;
      klass.prototype = new subclass;
      parent.subclasses.push(klass);
    }
 
    for (var i = 0; i < properties.length; i++)
      klass.addMethods(properties[i]);
 
    if (!klass.prototype.initialize)
      klass.prototype.initialize = function() { };
 
    klass.prototype.constructor = klass;
    return klass;
  }
 
  function addMethods(source) {
    var ancestor   = this.superclass && this.superclass.prototype;
    var properties = Object.keys(source);
 
    if (!Object.keys({ toString: true }).length) {
      if (source.toString != Object.prototype.toString)
        properties.push("toString");
      if (source.valueOf != Object.prototype.valueOf)
        properties.push("valueOf");
    }
 
    for (var i = 0, length = properties.length; i < length; i++) {
      var property = properties[i], value = source[property];
      if (ancestor && Object.isFunction(value) &&
          value.argumentNames()[0] == "$super") {
        var method = value;
        value = (function(m) {
          return function() { return ancestor[m].apply(this, arguments); };
        })(property).wrap(method);
 
        value.valueOf = method.valueOf.bind(method);
        value.toString = method.toString.bind(method);
      }
      this.prototype[property] = value;
    }
 
    return this;
  }
 
  return {
    create: create,
    Methods: {
      addMethods: addMethods
    }
  };
})();

Object.extend(Function.prototype, (function() {
  var slice = Array.prototype.slice;
 
  function update(array, args) {
    var arrayLength = array.length, length = args.length;
    while (length--) array[arrayLength + length] = args[length];
    return array;
  }
 
  function merge(array, args) {
    array = slice.call(array, 0);
    return update(array, args);
  }
 
 
  function bind(context) {
    if (arguments.length < 2 && Object.isUndefined(arguments[0])) return this;
    var __method = this, args = slice.call(arguments, 1);
    return function() {
      var a = merge(args, arguments);
      return __method.apply(context, a);
    }
  }
 
  function delay(timeout) {
    var __method = this, args = slice.call(arguments, 1);
    timeout = timeout * 1000;
    return window.setTimeout(function() {
      return __method.apply(__method, args);
    }, timeout);
  }
 
  function defer() {
    var args = update([0.01], arguments);
    return this.delay.apply(this, args);
  }
  
  function argumentNames() {
    var names = this.toString().match(/^[\s\(]*function[^(]*\(([^)]*)\)/)[1]
      .replace(/\/\/.*?[\r\n]|\/\*(?:.|[\r\n])*?\*\//g, '')
      .replace(/\s+/g, '').split(',');
    return names.length == 1 && !names[0] ? [] : names;
  }
  
  function wrap(wrapper) {
    var __method = this;
    return function() {
      var a = update([__method.bind(this)], arguments);
      return wrapper.apply(this, a);
    }
  }
  
  return {
    bind: bind,
    delay: delay,
    defer: defer,
    wrap: wrap,
    argumentNames: argumentNames
  }
})());

function $A(iterable) {
  if (!iterable) return [];
  if ('toArray' in Object(iterable)) return iterable.toArray();
  var length = iterable.length || 0, results = new Array(length);
  while (length--) results[length] = iterable[length];
  return results;
}