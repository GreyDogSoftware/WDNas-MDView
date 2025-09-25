if (typeof Toast === 'undefined'){
  Toast={
    _obj: null,
    _window: null,
    _title: null,
    _body: null,
    defaultClass: 'toast',
    defaultClassError: 'toast bg-danger-subtle',
    defaultClassSuccess: 'toast bg-success-subtle',
    defaultClassWarning: 'toast bg-warning-subtle',
    defaultClassInfo: 'toast bg-primary',
    defaultTitle: 'Notification toast',
    resetTitleOnEmpty: true,
    create: function(){
      // Creating the reference to the main window container
      this._window = document.getElementById('toast-window')
      // Creating the reference to the title node
      this._title = this._window.querySelector('#toast-title');
      // Creating the reference to the body node
      this._body =  this._window.querySelector('#toast-body');
      this._obj = bootstrap.Toast.getOrCreateInstance(this._window)
    },
    setContent: function(body, title){
      this.setTitle(title);
      this.setBody(body);
    },
    setTitle: function(titleText){
      if(this._title!==null){
        if(titleText){
          this._title.innerText=titleText;
        }else{
          if (resetTitleOnEmpty){this._title.innerText=this.defaultTitle;}
        }
      }
    },
    setBody: function(bodyText){
      if(this._body!==null){this._body.innerText=bodyText;}
    },
    show(){
      this._window.setAttribute("class", this.defaultClass);
      // Reference to the main class so it can be used inside the overloads.
      let _ref = this;
      let _show0 = function(){
        _ref._obj.show();
      };
      let _show1 = function(body){
        _ref.setTitle(null);
        _ref.setBody(body);
        _ref._obj.show();
      };
      let _show2 = function(body, title){
        _ref.setTitle(title);
        _ref.setBody(body);
        _ref._obj.show();
      };
      if (arguments.length === 0){
        _show0();
      }else if (arguments.length === 1){
        _show1(arguments[0]);
      }else if (arguments.length === 2){
        _show2(arguments[0], arguments[1]);
      }
    },
    showError(){
      this._window.setAttribute("class", this.defaultClassError);
      // Reference to the main class so it can be used inside the overloads.
      let _ref = this;
      let _show0 = function(){
        _ref._obj.show();
      };
      let _show1 = function(body){
        _ref.setTitle(null);
        _ref.setBody(body);
        _ref._obj.show();
      };
      let _show2 = function(body, title){
        _ref.setTitle(title);
        _ref.setBody(body);
        _ref._obj.show();
      };
      if (arguments.length === 0){
        _show0();
      }else if (arguments.length === 1){
        _show1(arguments[0]);
      }else if (arguments.length === 2){
        _show2(arguments[0], arguments[1]);
      }
    },
    showSuccess(){
      this._window.setAttribute("class", this.defaultClassWarning);
      // Reference to the main class so it can be used inside the overloads.
      let _ref = this;
      let _show0 = function(){
        _ref._obj.show();
      };
      let _show1 = function(body){
        _ref.setTitle(null);
        _ref.setBody(body);
        _ref._obj.show();
      };
      let _show2 = function(body, title){
        _ref.setTitle(title);
        _ref.setBody(body);
        _ref._obj.show();
      };
      if (arguments.length === 0){
        _show0();
      }else if (arguments.length === 1){
        _show1(arguments[0]);
      }else if (arguments.length === 2){
        _show2(arguments[0], arguments[1]);
      }
    },
    showWarning(){
      this._window.setAttribute("class", this.defaultClassWarning);
      // Reference to the main class so it can be used inside the overloads.
      let _ref = this;
      let _show0 = function(){
        _ref._obj.show();
      };
      let _show1 = function(body){
        _ref.setTitle(null);
        _ref.setBody(body);
        _ref._obj.show();
      };
      let _show2 = function(body, title){
        _ref.setTitle(title);
        _ref.setBody(body);
        _ref._obj.show();
      };
      if (arguments.length === 0){
        _show0();
      }else if (arguments.length === 1){
        _show1(arguments[0]);
      }else if (arguments.length === 2){
        _show2(arguments[0], arguments[1]);
      }
    },
    showInfo(){
      this._window.setAttribute("class", this.defaultClassInfo);
      // Reference to the main class so it can be used inside the overloads.
      let _ref = this;
      let _show0 = function(){
        _ref._obj.show();
      };
      let _show1 = function(body){
        _ref.setTitle(null);
        _ref.setBody(body);
        _ref._obj.show();
      };
      let _show2 = function(body, title){
        _ref.setTitle(title);
        _ref.setBody(body);
        _ref._obj.show();
      };
      if (arguments.length === 0){
        _show0();
      }else if (arguments.length === 1){
        _show1(arguments[0]);
      }else if (arguments.length === 2){
        _show2(arguments[0], arguments[1]);
      }
    }
  }
  Toast.create();
}