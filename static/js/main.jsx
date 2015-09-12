var Editor = React.createClass({
  getInitialState: function() {
    return {
      scenes: [],
      editingScene: {}
    };
  },
  componentDidMount: function () {

  },
  componentDidUpdate: function () {

  },
  _editScence: function (i) {
    this.setState({
      editingScene: this.state.scenes[i]
    });
    this.props.config('sceneIndex', i);
    this.props.config('commandIndex', 0);
  },
  _onChangeName: function (e) {
    var data = this.state.editingScene;
    data['name'] = e.target.value;
    this.setState({editingScene: data});
  },
  _showFileBrowser: function (category) {
    var _self = this;
    var xhr = new XMLHttpRequest();
    xhr.open('GET', '/api/' + category + '/', true);
    xhr.onreadystatechange = function () {
      if (xhr.readyState != 4 || xhr.status != 200)
        return;

      var DOMNode = _self.refs.fileBrowser.getDOMNode();
      var response = JSON.parse(xhr.responseText);
      React.unmountComponentAtNode(DOMNode);
      React.render(<FileBrowser files={response} category={category} _event={_self._selectFile} />, DOMNode);
    };
    xhr.send();
  },
  _selectFile: function (e) {
    var DOMNode = e.currentTarget,
      dataset = DOMNode.dataset,
      category = dataset['category'],
      name = dataset['name'],
      url = DOMNode.children[0].src;

    if (category == 'backgrounds')
      this.props.config('backgroundImage', url);
    if (category == 'roles')
      this.props.config('roles', [{image: url}]);

    var command = 'sp "'+ (category == 'backgrounds' ? 'bg' : 'role') +'", {"name": "'+ name +'" }\n';
    this._insertCommand(command);
  },
  _insertCommand: function (command) {
    var editingScene = this.state.editingScene,
      commandsString = this.refs.commandsEditor.getDOMNode().value,
      caretPos = this._getInputSelection().end;
    commandsString = commandsString.slice(0, caretPos) + command + commandsString.slice(caretPos);

    editingScene.commands = commandsString.split('\n');
    this.setState({editingScene: editingScene});
  },
  _editCommands: function (e) {
    var editingScene = this.state.editingScene,
      commandsString = e.target.value;

    editingScene.commands = commandsString.split('\n');
    this.setState({editingScene: editingScene});
  },
  _getInputSelection: function(e) {
    var el = this.refs.commandsEditor.getDOMNode(),
      start = 0, end = 0, normalizedValue, range,
      textInputRange, len, endRange;

    if (typeof el.selectionStart == "number" && typeof el.selectionEnd == "number") {
      start = el.selectionStart;
      end = el.selectionEnd;
    } else {
      range = document.selection.createRange();

      if (range && range.parentElement() == el) {
        len = el.value.length;
        normalizedValue = el.value.replace(/\r\n/g, "\n");

        // Create a working TextRange that lives only in the input
        textInputRange = el.createTextRange();
        textInputRange.moveToBookmark(range.getBookmark());

        // Check if the start and end of the selection are at the very end
        // of the input, since moveStart/moveEnd doesn't return what we want
        // in those cases
        endRange = el.createTextRange();
        endRange.collapse(false);

        if (textInputRange.compareEndPoints("StartToEnd", endRange) > -1) {
          start = end = len;
        } else {
          start = -textInputRange.moveStart("character", -len);
          start += normalizedValue.slice(0, start).split("\n").length - 1;

          if (textInputRange.compareEndPoints("EndToEnd", endRange) > -1) {
            end = len;
          } else {
            end = -textInputRange.moveEnd("character", -len);
            end += normalizedValue.slice(0, end).split("\n").length - 1;
          }
        }
      }
    }
    return {
      start: start,
      end: end
    };
  },
  _previewImage: function (e) {
    var reader = new FileReader(),
      input = e.target,
      value;

    reader.onload = function(e) {
      value = e.target.result;
      this.props.onConfig({name: 'backgroundImage', value: 'url('+value+')'});
    }.bind(this);

    for (var i = 0; i < input.files.length; i++) {
      var image = input.files[i];
      reader.readAsDataURL(image)
    }
  },
  _createScene: function () {
    var scenes = this.state.scenes;
    scenes.push({background: null, dialogs: [], name: '[NEW]'});
    this.props.config('scenes', scenes);
  },
  _saveScene: function (e) {
    var commands = this.state.editingScene.commands,
      data = {},
      xhr = new XMLHttpRequest();
    data['commands'] = commands;
    data['name'] = this.state.editingScene.name;
    data['story_id'] = this.props.storyId;
    if (this.state.editingScene.id)
      xhr.open('PUT', '/api/scenes/' + this.state.editingScene.id + '/');
    else
      xhr.open('POST', '/api/scenes/');
    xhr.send(JSON.stringify(data));
    e.preventDefault()
  },
  _deleteScene: function (e) {
    if (confirm('Delete? (cannot undo)')) {
      var scenes = this.state.scenes,
        sceneIndex = this.state.scenes.map(function (scene) {return scene.id}).indexOf(this.state.editingScene.id);
      var xhr = new XMLHttpRequest();
      xhr.open('DELETE', '/api/scenes/' + this.state.editingScene.id + '/');
      xhr.send();

      scenes.splice(sceneIndex, 1);
      this.setState({editingScene: {}});
      this.props.config({scenes: scenes});
    }
  },
  render: function () {
    var configStyle = (Object.keys(this.state.editingScene).length != 0) ? {display: 'inline-block'} : {display: 'none'},
      commandsString = this.state.editingScene.commands ? this.state.editingScene.commands.join('\n').replace(/&gt;/g, '>') : '';

    return (
      <div className="editor fg-grid">
        <div className="timeline fg-1-2">
          {this.state.scenes.map(function (scene, i) {
            return <div key={i} className="scene" onClick={this._editScence.bind(this, i)}>{scene.name}</div>
          }, this)}
          <button onClick={this._createScene} className="btn-new">New</button>
        </div>
        <div className="config fg-1-2" style={configStyle}>
          <label>Name: <input name="" value={this.state.editingScene.name} onChange={this._onChangeName} /></label>
          <label>
            Scripts:
            <div className="toolbar">
            <a onClick={this._showFileBrowser.bind(this, 'backgrounds')}>BGD</a>
            <a onClick={this._showFileBrowser.bind(this, 'roles')}>Role</a>
            </div>
            <textarea ref="commandsEditor" className="commands" onChange={this._editCommands} value={commandsString}></textarea>
            <button onClick={this._saveScene}>Save</button><button onClick={this._deleteScene}>Delete</button>
          </label>
        </div>
        <div ref="fileBrowser" />
      </div>
    );
  }
});

var FileBrowser = React.createClass({
  _close: function () {
    React.unmountComponentAtNode(this.getDOMNode().parentNode);
  },
  render: function () {
    return (
      <div className="file-browser"><a className="close" onClick={this._close}>x</a>
        {this.props.files.map(function (file, i) {
          var style = file.isSelected ? {'backgroundColor' : '#000'} : {};
          return (
            <a key={i} data-name={file.name} data-category={this.props.category} className="fg-1-12" style={style} onClick={this.props._event}><img className="item" src={file.image} /><h4>{file.name}</h4></a>
          );
        }, this)}
      </div>
    );
  }
});

var MainWindow = React.createClass({
  getInitialState: function() {
    return {
      scenes: [],
      sceneIndex: 0,
      commandIndex: 0,
      isEditing: false,
      roles: [],
      backgroundImage: null
    };
  },
  componentDidMount: function () {
    if (this.props.storyId) {
      var xhr = new XMLHttpRequest();
      xhr.open('GET', '/api/stories/' + this.props.storyId + '/', true);
      xhr.onreadystatechange = function () {
        if (xhr.readyState != 4 || xhr.status != 200)
          return;
        var response = JSON.parse(xhr.responseText),
          scenes = response.scenes;

        this.setState({scenes: response.scenes});
        scenes = response.scenes;
      }.bind(this);
      xhr.send();
    }
  },
  componentDidUpdate: function () {    
    this.refs.editor.setState({scenes: this.state.scenes});
  },
  _config: function (name, value) {
    this.state[name] = value;
    this.forceUpdate();
  },
  _toggleEditor: function (e) {
    if (this.props.mountEditor) {
      e.preventDefault();
      var DOMNode = this.refs.editor.getDOMNode()

      if (DOMNode.style.display == '' || DOMNode.style.display == 'none')
        DOMNode.style.display = 'block';
      else
        DOMNode.style.display = 'none';

      this.setState({isEditing: !this.state.isEditing});
    }

  },
  _getURLAsName: function (category, name, callback) {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', '/api/' + category + '/?name=' + name, true);
    xhr.onreadystatechange = function () {
      if (xhr.readyState != 4 || xhr.status != 200)
        return;
      var response = JSON.parse(xhr.responseText)[0];
      callback(response.image);
    };
    xhr.send();
  },
  _setBackgroundImage: function (args) {
    if (args === null)
      this.setState({backgroundImage: null});
    else {
      this._getURLAsName('backgrounds', args.name, function (url) {
        this.setState({backgroundImage: url});
      }.bind(this));
    }
  },
  _setRole: function (args, isDelete) {
    var roles = this.state.roles;
    if (isDelete) {
      var roleIndex = roles.map(function (role) {return role.name}).indexOf(args.name);
      if (roleIndex !== -1)
        roles.splice(roleIndex, 1);
    }
    else
      this._getURLAsName('roles', args.name, function (url) {
        roles.push({
          name: args.name,
          image: url
        });
      });

    this.setState({roles: roles});
  },
  _play: function (e) {
    if (this.state.isEditing === true) return e.preventDefault();
    if (this.state.scenes[this.state.sceneIndex] === undefined) return e.preventDefault();
    // if ((this.state.sceneIndex === this.state.scenes.length - 1) && (this.state.commandIndex === this.state.scenes[this.state.sceneIndex].dialogs.length)) 

    var scene = this.state.scenes[this.state.sceneIndex],
      command = scene.commands[this.state.commandIndex];

    if (command === '') {
      this.setState({commandIndex: this.state.commandIndex + 1}, function () {
        this.getDOMNode().click();
      }.bind(this));
      //return e.preventDefault();
    }

    if (command !== undefined) {
      var commaFirstPos = command.indexOf(',');
      if (command.substr(0,4) == '&gt;')
        React.findDOMNode(this.refs.dialogBox).innerHTML = command.substr(4);
      else if (commaFirstPos === -1) {
        switch (command) {
          case 'spdelete "bg"':
            this._setBackgroundImage(null);
          break;
        }
      }
      else {
        var method = command.substr(0, command.indexOf(',')).trim(),
          args = command.substr(command.indexOf(',') + 1).trim();

        try {
          args = JSON.parse(args);
        }
        catch (e) {
          console.error(e);
        }

        switch (method) {
          case 'sp "bg"':
            this._setBackgroundImage(args);
          break;
          case 'sp "role"':
            this._setRole(args);
          break;
          case 'spdelete "role"':
            this._setRole(args, true);
            console.log(args)
          break;
        }
      }
    }

    if (this.state.commandIndex <= scene.commands.length - 1)
      this.setState({commandIndex: this.state.commandIndex + 1});
    else {
      if (this.state.sceneIndex !== this.state.scenes.length - 1)
        this.setState({
          commandIndex: 0,
          sceneIndex: this.state.sceneIndex + 1
        });
    }

  },
  render: function () {
    var mainWindowStyle = {};
    mainWindowStyle['backgroundImage'] = this.state.backgroundImage ? 'url(' + this.state.backgroundImage + ')' : null;
    return (
      <div className="main-window" onClick={this._play} onContextMenu={this._toggleEditor} style={mainWindowStyle}>
        {this.state.roles.map(function (role, i) {
          var style = {
            backgroundImage: 'url(' + role.image + ')'
          };
          return (<div key={i} className="role" style={style}></div>);
        }, this)}
        <div className="dialog-box" ref="dialogBox"></div>
        <Editor ref="editor" config={this._config} storyId={this.props.storyId} />
      </div>
    );
  }
});
