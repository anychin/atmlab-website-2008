var AjaxForm = Class.create({
	initialize: function(el){
		this.container = el;
		this.errorContainer = null;
		this.fields = {};
		this.valid = true;
		this.annotations = {};
		var submit_button = jq("input:submit,input:image, .submit", el);
		if(submit_button.length > 0)
			submit_button.click(this.submitForm.bind(this));
		this.parseFields();
	},
	getId: function(){
		return this.id;
	},
	setFormError: function(error){
		if(this.errorContainer && this.annotations[error])
			this.errorContainer.html(this.annotations[error]);
	},
	clearFormError: function(){
		if(this.errorContainer)
			this.errorContainer.html('');
	},
	submit: function(){
		this.submitForm();
	},
	submitForm: function(){
		this.valid = true;
		this.clearFormError();
		this.validate();
		if(this.valid)
			this.sendFormData();
		else
			this.setFormError('error');
		return false;
	},
	validate: function(){
		for(var name in this.fields)
		{
			this.fields[name].validate();
		}
		if(this.validateFn)
			this.validateFn();
	},
	sendFormData: function(){
		var parameters = {}
		for(var name in this.fields){
			parameters[name] = this.fields[name].val;
		}
		var method = 'post';
		if(this.annotations.method)
			method = this.annotations.method;
		var url = "/" + this.annotations.name + "/form";
		if(AjaxForm.options.prefix)
			url = AjaxForm.options.prefix + url;
		if(this.annotations.url)
			url = this.annotations.url;
		if(this.beforeSendFn)
			this.beforeSendFn(parameters);
		jq.ajax({
			type:method,
			data:parameters,
			cache:false,
			url:url,
			dataType:"json",
			success:(function(response){
				var res = response;
				if(this.onResponse)
				{
					this.onResponse(res);
					return;
				}
				if(res.valid){
					if(this.onSuccess)
					{
						this.onSuccess(res);
						return;
					}	
					if(this.annotations.successUrl){
						if(this.annotations.successUrl == 'this')
							document.location.href = document.location.href;
						else
							document.location.href = this.annotations.successUrl;
					}else
						this.setFormError('success');
				}
				else{
					for(var i in res.errors){
						if(this.fields[i])
							this.fields[i].addError(res.errors[i]);
					}
					this.setFormError('error');	
				}
			}).bind(this),
			error:(function(res){
				if(this.onError)
					this.onError(res);
				else
					this.setFormError('failure');
			}).bind(this)
		});
	},
	parseFields: function(){
		this.getFormAnnotations();
		var err_cont = jq(".formErrorMessage", this.container);
		jq(".formItem", this.container).each((function(i, item){
			item = jq(item);
			var annotations = this.getAnnotations(item);
			var name = this.getName(item, annotations);
			this.fields[name] = AjaxFormItemFactory.getItem(
				item,
				this,
				name,
				this.getType(item, annotations),
				this.getErrorContainer(item, annotations),
				annotations);
		}).bind(this));
		if(this.annotations.validate)
			this.validateFn = this.parseMethod(this.annotations.validate).bind(this);
		if(this.annotations.beforeSend)
			this.beforeSendFn = this.parseMethod(this.annotations.beforeSend).bind(this);
		if(this.annotations.onError)
			this.onError = this.parseMethod(this.annotations.onError).bind(this);
		if(this.annotations.onResponse)
			this.onResponse = this.parseMethod(this.annotations.onResponse).bind(this);
		if(this.annotations.onSuccess)
			this.onSuccess = this.parseMethod(this.annotations.onSuccess).bind(this);
	},
	getType: function(element, annotations){
		if(annotations.type)
			return annotations.type;
		var input = jq("input", element);
		if(input.length > 0)
			return input[0].type;
		if(jq("select", element).length > 0)
			return "select";
		if(jq("textarea", element).length > 0)
			return "textarea";
		return undefined;
	},
	getName: function(element, annotations){
		if(annotations.name)
			return annotations.name;
		var inp = jq("input, select, textarea", element);
		if(inp.length == 0)
			return "";
		return inp.attr("name");
	},
	getErrorContainer: function(element, annotations){
		if(annotations.errorMessage)
			return jq("#" + annotations.errorMessage);
		var cont = jq(".errorMessage", element);
		if(cont.length > 0)
			return cont;
		return null;
	},
	getFormAnnotations: function(){
		var annotations = jq("> .annotation", this.container);
		if(annotations.length == 0)
			alert("add annotation to form");
		this.annotations = this.parseAnnotationsString(annotations.html());
	},
	getAnnotations: function(element){
		var annotations = jq(".annotation", element);
		if(annotations.length == 0)
			return {};
		annotations = this.parseAnnotationsString(annotations.html());
		annotations.errors = {};
		for(var annotation in annotations)
			if(annotation.indexOf("error.") > -1)
					annotations.errors[annotation.replace("error.","")] = annotations[annotation];
		return annotations;
	},
	parseAnnotationsString: function(str){
		if(str == "")
			return {};
		var annotations = str.split('|');
		var res = {};
		for(var i = 0; i < annotations.length; i++)
		{
			var s = jq.trim(annotations[i]);
			var a = s.split('=');
			res[a[0]] = a[1];
		}
		return res;
	},
	parseMethod: function(str){
		var parts = str.split('.');
		var target = window;
		var method = parts.pop();
		for(var i = 0; i < parts.length; i++)
			target = target[parts[i]];
		return target[method];
	}
});

AjaxForm.options = {};
AjaxForm.init = function(){
	AjaxForm.forms = {};
	jq(".form").each(function(i, el){
		var f = new AjaxForm(jq(el));
		AjaxForm.forms[f.annotations.name] = f;
	});
}

var AjaxFormItemFactory = Class.create({});

AjaxFormItemFactory.getItem = function(element, form, name, type, errorContainer, annotations){
	if(type == "textarea")
		return new TextAreaFormItem(element, form, name, type, errorContainer, annotations);
	if(type == "float")
		return new FloatFormItem(element, form, name, type, errorContainer, annotations);
	if(type == "integer")
		return new IntegerFormItem(element, form, name, type, errorContainer, annotations);
	if(type == "email")
		return new EmailFormItem(element, form, name, type, errorContainer, annotations);
	if(type == "select")
		return new SelectFormItem(element, form, name, type, errorContainer, annotations);
	if(type == "checkbox")
		return new CheckboxFormItem(element, form, name, type, errorContainer, annotations);
	if(type == "radio")
		return new RadioFormItem(element, form, name, type, errorContainer, annotations);
	if(type == "date")
		return new DateFormItem(element, form, name, type, errorContainer, annotations);
	if(type == "datepicker")
		return new DatePickerFormItem(element, form, name, type, errorContainer, annotations);
	if(type == "richtext")
		return new RichtextFormItem(element, form, name, type, errorContainer, annotations);
	if(type == "multiselect")
		return new MultiSelectFormItem(element, form, name, type, errorContainer, annotations);
	if(type == "multicheck")
		return new MultiCheckboxFormItem(element, form, name, type, errorContainer, annotations);
	if(annotations.clazz)
		return new window[annotations.clazz](element, form, name, type, errorContainer, annotations);
	return new TextFormItem(element, form, name, type, errorContainer, annotations);
};

var TextFormItem = Class.create({
	initialize: function(element, form, name, type, errorContainer, annotations){
		this.element = element;
		this.form = form;
		this.name = name;
		this.type = type;
		this.val = null;
		this.errorContainer = errorContainer;
		this.annotations = annotations;
		if(this.annotations.validate)
			this.validateFn = this.form.parseMethod(this.annotations.validate).bind(this);
		if(this.annotations.init)
			this.initFn = this.form.parseMethod(this.annotations.init).bind(this);
		if(this.annotations.value)
			this.valueFn = this.form.parseMethod(this.annotations.value).bind(this);
		this.init();
	},
	init: function(){
		if(this.initFn)
		{
			this.initFn();
			return;
		}	
		this.control = jq("input", this.element);
		if(!this.annotations["no-enter"])
			this.control.keypress((function(event){
				if(event.which == 13)
					this.form.submitForm(null);
			}).bind(this));
	},
	value: function(){
		if(this.valueFn)
		{
			this.val = this.valueFn();
			return this.val;
		}
		var enforce = arguments.length > 0 && arguments[0];
		if(!enforce)
			return this.val;
		this.val = this.control.val();
		if(this.val == '<p>&nbsp;</p>')
			this.val = '';
		return this.val;
	},
	validate: function(){
		this.clearError();
		this.value(true);
		if(this.validateFn)
		{
			this.validateFn();
			return;
		}
		if(!this.val && this.annotations.required == "true")
		{
			this.addError('required');
			return;
		}
		if(this.annotations.length){
			var l = parseInt(this.annotations.length);
			if(!this.val || this.val.length != l)
				this.addError("wrong.length");
		}
		if(this.annotations.minlength){
			var minl = parseInt(this.annotations.minlength);
			if(this.val.length < minl)
				this.addError("short.length");
		}
		if(this.annotations.maxlength){
			var maxl = parseInt(this.annotations.maxlength);
			if(this.val.length > maxl)
				this.addError("long.length");
		}
	},
	addError: function(error){
		this.form.valid = false;
		this.element.removeClass("formItem");
		this.element.addClass("formItemError");
		if(this.errorContainer && this.annotations.errors[error])
			this.errorContainer.html(this.annotations.errors[error]);
	},
	clearError: function(){
		this.element.removeClass("formItemError");
		this.element.addClass("formItem");
		if(this.errorContainer)
			this.errorContainer.html('');
	}
});

var TextAreaFormItem = Class.create(TextFormItem, {
	init: function(){
		this.control = jq("textarea", this.element);
		if(!this.annotations["no-enter"])
			this.control.keypress((function(event){
				if(event.which == 13 && event.ctrlKey)
				{
					this.form.submitForm(null);
				}	
			}).bind(this));
	},
	value: function(){
		if(this.valueFn)
		{
			this.val = this.valueFn();
			return this.val;
		}
		this.val = this.control.val();
		return this.val;
	}
});

var IntegerFormItem = Class.create(TextFormItem, {
	validate: function($super){
		$super();
		if(!this.form.valid)
			return;
		var regExp = /^([\d]+)$/;
		if(!this.val.match(regExp))
			this.addError("wrong.format");
	}
});

var FloatFormItem = Class.create(TextFormItem, {
	validate: function($super){
		$super();
		if(!this.form.valid)
			return;
		var regExp = /^([\d]+(\.)?[\d]*)$/;
		if(!this.val.match(regExp))
			this.addError("wrong.format");
	}
});

var SelectFormItem = Class.create(TextFormItem, {
	init: function(){
		this.control = jq("select", this.element);
	},
	value: function(){
		if(this.valueFn)
		{
			this.val = this.valueFn();
			return this.val;
		}
		this.val = this.control.val();
		if(this.val == 'nil')
			this.val = null;
	}
});

var CheckboxFormItem = Class.create(TextFormItem, {
	init: function(){},
	value: function(){
		if(this.valueFn)
		{
			this.val = this.valueFn();
			return this.val;
		}
		var enforce = arguments.length > 0 && arguments[0];
		if(!enforce)
			return this.val;
		this.val = jq("input:checked", this.element).length > 0;
		return this.val;
	}
});

var RadioFormItem = Class.create(TextFormItem, {
	init: function(){},
	value: function(){
		if(this.valueFn)
		{
			this.val = this.valueFn();
			return this.val;
		}
		var enforce = arguments.length > 0 && arguments[0];
		if(!enforce)
			return this.val;
		var c = jq("input[type=radio]", this.element).is("[checked]");
		if(!c)
			return null;
		this.val = jq("input[type=radio][checked]", this.element).val();
		return this.val;
	}
});

var EmailFormItem = Class.create(TextFormItem, {
	validate: function($super){
		$super();
		if(!this.form.valid)
			return;
		var regExp = /^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i;
		if(!this.val.match(regExp))
			this.addError("wrong.format");
	}
});


var DateFormItem = Class.create(TextFormItem, {
	init: function(){
		this.controls = {};
		this.controls.day = jq("select[name=day]", this.element);
		this.controls.month = jq("select[name=month]", this.element);
		this.controls.year = jq("select[name=year]", this.element);
		if(this.controls.year.length == 0)
			this.controls.year = null;
		this.controls.time = jq("input[name=time]", this.element);
		if(this.controls.time.length == 0)
			this.controls.time = null;
		this.controls.yearInp = jq("input[name=year]", this.element);
		if(this.controls.yearInp.length == 0)
			this.controls.yearInp = null;
	},
	value: function(){
		var enforce = arguments.length > 0 && arguments[0];
		if(!enforce)
			return this.val;
		var day, month, year, time = '';
		var val = 1;
		if(this.controls.day.val() != 'nill')
			day = this.controls.day.val();
		else
			val = null;
		if(this.controls.month.val() != 'nill')
			month = this.controls.month.val();
		else
			val = null;
		if(this.controls.year)
		{
			if(this.controls.year.val() != 'nill')
				year = this.controls.year.val();
			else
				val = null;
		}
		if(this.controls.yearInp && this.controls.yearInp.css("display") != "none")
		{
			var rp = new RegExp("[1-9][0-9][0-9][0-9]");
			if(this.controls.yearInp.val().match(rp))
				year = this.controls.yearInp.val();
			else
				val = null;
		}
		if(val == null)
		{
			this.val = null;
			return null;
		}
		if(this.controls.time && jq.trim(this.controls.time.val()) != ""){
			var v = jq.trim(this.controls.time.val());
			var regExp = new RegExp("\\d\\d:\\d\\d");
			if(v.match(regExp)){
				var vs = v.split(":");
				var h = parseInt(vs[0]);
				var m = parseInt(vs[1]);
				if(h > 23 || m > 59)
				{
					this.addError("not.valid");
					time = null;
				}
			}else{
				this.addError("not.valid");
				time = null;
			}
			if(time != null)
				time = v + ":00";
		}
		var result = year + "-";
		var date = new Date();
		date.setYear(year);
		date.setMonth(month);
		date.setDate(day);
		if(date.getMonth() != month)
			this.addError("not.valid");
		if(month.length < 2)
			result += "0" + (eval(month) + 1) + "-";
		else
			result += (eval(month) + 1) + "-";
		if(day.length < 2)
			result += "0" + day;
		else
			result += day;
		if(time != null)
			result += " " + time;
		this.val = result;
		return this.val;
	}
});

var DatePickerFormItem = Class.create(TextFormItem, {
	init: function(){
		this.control = jq("input", this.element);
		this.control.datepicker({});
	},
	value: function(){
		var d = this.control.val().split("\.");
		this.val = d[2] + "-" + d[1] + "-" + d[0]
		return this.val;
	}
});

var RichtextFormItem = Class.create(TextFormItem, {
	init: function(){
		this.control = jq('textarea', this.element);
	},
	value: function(){
		this.val = tinyMCE.get(this.control.attr("id")).getContent();
		return this.val;
	}
});

var MultiSelectFormItem = Class.create(TextFormItem, {
	init: function(){
		this.control = jq("select", this.element);
	},
	value: function(){
		var enforce = arguments.length > 0 && arguments[0];
		if(!enforce)
			return this.val;
		var v = this.control.val();
		if(!v)
			this.val = null;
		else
			this.val = v.join(",");
	}
});

var MultiCheckboxFormItem = Class.create(TextFormItem, {
	init: function(){},
	value: function(){
		var enforce = arguments.length > 0 && arguments[0];
		if(!enforce)
			return this.val;
		var v = jq("checbox:checked", this.element).val();
		if(!v)
			this.val = null;
		else
			this.val = v.join(",");
		return this.val;
	}
});