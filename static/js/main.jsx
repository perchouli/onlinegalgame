var Editor = React.createClass({
  getInitialState: function() {
    return {
      scenes: [],
      editingScene: {}
    };
  },
  _editScence: function (i) {
    this.setState({
      editingScene: this.state.scenes[i]
    });
    this.props.config('sceneIndex', i);
    this.props.config('commandIndex', 0);
    this.props.config('backgroundImage', null);
    this.props.config('roles', []);
  },
  _onChangeName: function (e) {
    var data = this.state.editingScene;
    data['name'] = e.target.value;
    this.setState({editingScene: data});
  },
  _renderFileBrowser: function (category) {
    var _self = this;
    var xhr = new XMLHttpRequest();
    xhr.open('GET', '/api/' + category + '/', true);
    xhr.onreadystatechange = function () {
      if (xhr.readyState != 4 || xhr.status != 200)
        return;

      var DOMNode = _self.refs.fileBrowser;
      var response = JSON.parse(xhr.responseText);
      ReactDOM.unmountComponentAtNode(DOMNode);
      ReactDOM.render(<FileBrowser files={response} category={category} _event={_self._selectFile} />, DOMNode);
    };
    xhr.send();
  },
  _toggleBranchesEdit: function (e) {
    e.preventDefault();
    var DOMNode = document.getElementById('branches-edit');
    DOMNode.style.display = DOMNode.style.display == 'block' ? 'none' : 'block';
  },
  _saveBranches: function (e) {
    var DOMNode = document.querySelectorAll('#branches-edit ul li'),
      branches = [];
    for (var i = 0; i < DOMNode.length; i++) {
      var li = DOMNode[i],
        label = li.querySelector('input[name="label"]').value,
        name = li.querySelector('select[name="name"]').value;
      branches.push({label: label, name: name});
    }
    var command = '\nsp "branches",' + JSON.stringify(branches);
    this._insertCommand(command);
    this._toggleBranchesEdit(e);
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
      this.props.config('roles', [{url: url}]);

    var command = '\nsp "'+ (category == 'backgrounds' ? 'bg' : 'role') +'", {"name": "'+ name +'" }\n';
    this._insertCommand(command);
  },
  _insertCommand: function (command) {
    var editingScene = this.state.editingScene,
      commandsString = this.refs.commandsEditor.value,
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
    var el = this.refs.commandsEditor,
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
    var editingScene = this.state.editingScene,
      commands = editingScene.commands,
      data = {},
      xhr = new XMLHttpRequest();
    data['commands'] = commands;
    data['name'] = editingScene.name;
    data['story_id'] = this.props.storyId;

    xhr.onreadystatechange = function () {
      if (xhr.readyState != 4 || xhr.status != 200)
        return;
      var response = JSON.parse(xhr.responseText);
      if (response.length > 0) {
        editingScene.id = response[0].id;
        this.setState({editingScene: editingScene});
      }
      alert('Success!');
    }.bind(this);

    if (editingScene.id) {
      xhr.open('PUT', '/api/scenes/' + editingScene.id + '/');
    }
    else {
      xhr.open('POST', '/api/scenes/');
    }
    xhr.send(JSON.stringify(data));
    e.preventDefault()
  },
  _deleteScene: function (e) {
    if (confirm('Delete? (cannot undo)')) {
      var scenes = this.state.scenes,
        sceneIndex = this.state.scenes.map(function (scene) {return scene.id}).indexOf(this.state.editingScene.id);

      if (this.state.editingScene.id) {
        var xhr = new XMLHttpRequest();
        xhr.open('DELETE', '/api/scenes/' + this.state.editingScene.id + '/');
        xhr.send();
      }

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
              <a onClick={this._renderFileBrowser.bind(this, 'backgrounds')}>BGD</a>
              <a onClick={this._renderFileBrowser.bind(this, 'roles')}>Role</a>
              <a onClick={this._toggleBranchesEdit}>Branch</a>
              <div className="popup" id="branches-edit">
                <ul>
                  <li>
                    <input name="label" placeholder="Label" />
                    <select name="name">
                      {this.state.scenes.map(function (scene, i) {
                        return (<option key={i} value={scene.name}>{scene.name}</option>)
                      }, this)}
                    </select>
                  </li>
                  <li>
                    <input name="label" placeholder="Label" />
                    <select name="name">
                      {this.state.scenes.map(function (scene, i) {
                        return (<option key={i} value={scene.name}>{scene.name}</option>)
                      }, this)}
                    </select>
                  </li>
                </ul>
                <button onClick={this._saveBranches}>Save</button>
              </div>
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
  render: function () {
    return (
      <div className="file-browser"><a className="close" onClick={function(){ReactDOM.unmountComponentAtNode(React.findDOMNode(this).parentNode)}.bind(this)}>x</a>
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

var Branches = React.createClass({
  render: function () {
    return (
    <div>
      {this.props.branches.map(function (branch, i) {
        return (<a key={i} data-name={branch.name} className="branch" onClick={this.props._event}>{branch.label}</a>);
      }, this)}
    </div>
    );
  }
})

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
  _isMobile: function () {
    return (function(a){return (/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))) ? true : false;})(navigator.userAgent||navigator.vendor||window.opera);
  },
  _config: function (name, value) {
    this.state[name] = value;
    this.forceUpdate();
  },
  _toggleEditor: function (e) {
    if (this.props.mountEditor) {
      e.preventDefault();
      var DOMNode = ReactDOM.findDOMNode(this.refs.editor);

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
      this.setState({roles: roles});
    }
    else
      this._getURLAsName('roles', args.name, function (url) {
        roles.push({
          name: args.name,
          url: url,
          style: args
        });
        this.setState({roles: roles});
      }.bind(this));

  },
  _goto: function (e) {
    var name = e.currentTarget.dataset.name,
      sceneIndex = this.state.scenes.map(function (scene) {return scene.name;}).indexOf(name);
    this.setState({
      sceneIndex: sceneIndex,
      backgroundImage: null,
      roles: [],
      commandIndex: 0
    }, function () {
      ReactDOM.unmountComponentAtNode(this.refs.dialogBox);
      this._play();
    });
  },
  _play: function (e) {
    if (
      (this.state.isEditing === true) ||
      (this.state.scenes[this.state.sceneIndex] === undefined) ||
      (this.refs.dialogBox.children.length !== 0)
    )
      return e.preventDefault();

    if ((this.state.sceneIndex === this.state.scenes.length - 1) &&
      (this.state.commandIndex === this.state.scenes[this.state.sceneIndex].commands.length)) {
      alert('Ending');
      return e.preventDefault();
    }


    var scene = this.state.scenes[this.state.sceneIndex],
      command = scene.commands[this.state.commandIndex];

    if (command === '') {
      this.setState({commandIndex: this.state.commandIndex + 1}, function () {
        ReactDOM.findDOMNode(this).click();
      }.bind(this));
      //return e.preventDefault();
    }

    if (command !== undefined) {
      var commaFirstPos = command.indexOf(',');
      if (command.substr(0,4) == '&gt;')
        ReactDOM.findDOMNode(this.refs.dialogBox).innerHTML = command.substr(4);
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
          break;
          case 'sp "branches"':
            var DOMNode = this.refs.dialogBox;
            ReactDOM.unmountComponentAtNode(DOMNode);
            ReactDOM.render(<Branches branches={args} _event={this._goto} />, DOMNode);
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
      <div className="main-window" onClick={this._isMobile ? null : this._play} onTouchStart={this._play} onContextMenu={this._toggleEditor} style={mainWindowStyle}>
        {this.state.roles.map(function (role, i) {
          var style = role.style || {};
          style['backgroundImage'] = 'url(' + role.url + ')';
          return (<div key={i} className="role" style={style}></div>);
        }, this)}
        <div ref="dialogBox" className="dialog-box"></div>
        <Editor ref="editor" config={this._config} storyId={this.props.storyId} />
      </div>
    );
  }
});
