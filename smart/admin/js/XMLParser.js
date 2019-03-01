var XMLParser = {
	serialize:function(obj){
		var dom = XMLParser.getDom();
		return XMLParser.unparce(dom, dom, obj);
	},
	
	getDom:function(){
		var doc;
		try
		{
			if (window.ActiveXObject)
				doc = new ActiveXObject('MSXML2.DOMDocument');
			else
				doc = document.implementation.createDocument("", "", null);
		}
		catch(e){return null;}
		return doc;
	},
	
	getXML:function(xml){
		var doc;
		if(xml instanceof Object){
			if(xml.childNodes != undefined && xml.childNodes.length > 1)
				xml.removeChild(xml.childNodes[0]);
			return xml;
		}
		try{
			if (window.ActiveXObject){
				doc = new ActiveXObject('Microsoft.XMLDOM');
				doc.loadXML(xml);
			}
			else {
				var parser = new DOMParser();
				doc = parser.parseFromString(xml, 'text/xml');
			}
		}
		catch(e){return null;}
		return doc;
	},
	
	copyAttributes:function(node, res){
		if(node.attributes)
			for(var i = 0; i < node.attributes.length; i++)
				res[node.attributes[i].name] = node.attributes[i].value;
	},
	
	toXMLString:function(node){
		var str = '';
		try{
			if (window.ActiveXObject)
				str = node.xml;
			else{
				var s = new XMLSerializer();
				str = s.serializeToString(node);
				str = str.replace(/\n/g, '&#xA;');
				str = str.replace(/\r/g, '&#xD;');
			}
		}
		catch(e) {return "";}
		return str;
	},
	
	unparce:function(dom, root, obj){
		var d;
		for(var i in obj)
		{
			if(obj[i] instanceof Object)
			{
				if(!(obj[i] instanceof Array))
				{
					d = dom.createElement(i);
					d = XMLParser.unparce(dom, d, obj[i]);
					root.appendChild(d);
				}
				else
				{
					for(var j = 0; j < obj[i].length; j++)
					{
						d = dom.createElement(i);
						d = XMLParser.unparce(dom, d, obj[i][j]);
						root.appendChild(d);
					}
				}
			}
			else
			{
				if(i == "content")
				{
					var text = dom.createCDATASection(obj[i]);
					root.appendChild(text);
				}
				else
				{
					var v = obj[i];
					if(v != null){
						var attr = dom.createAttribute(i);
						
						if(typeof(v) == "boolean")
						{	
							if(v)
								v = "true";
							else
								v = "false";
						}
						attr.value = v;
						root.setAttributeNode(attr);
					}
				}
			}
				
		}
		return root;
	},
	
	deserialize:function(xml){
		var node = XMLParser.getXML(xml);
		if(node.childNodes != undefined && node.childNodes.length > 1)
			node.removeChild(node.childNodes[0]);
		var result = {};
		if(node == null || node.firstChild == null)
			return null;
		result[node.firstChild.nodeName] = {};
		var startNode = node.firstChild;
		var o = result[node.firstChild.nodeName];
		XMLParser.copyAttributes(node.firstChild, o);
		if(node.firstChild.firstChild != null)
			node = node.firstChild.firstChild;
		else
			return result;
		while(node)
		{
			if(!o[node.nodeName])
				o[node.nodeName] = [];
			o[node.nodeName].push({_parent:o, _name:node.nodeName});
			o = o[node.nodeName][o[node.nodeName].length - 1];
			XMLParser.copyAttributes(node, o);
			if(node.firstChild)
				node = node.firstChild;
			else
			{
				if(node.nextSibling)
				{
					o = o._parent;
					node = node.nextSibling;
				}
				else
				{
					while(node.parentNode && !node.nextSibling)
					{
						if(node == startNode){
							node = null;
							break;
						}
						else{
							o = o._parent;
							node = node.parentNode;
						}
					}
					if(o)
						o = o._parent;
					if(node && node.nextSibling)
						node = node.nextSibling;
				}
			}
			if(node && node.nodeType != 1)
			{
				if(o["content"] == undefined)
					o["content"] = "";
				o["content"] += node.nodeValue;
			}
		}
		return result;
	}
};