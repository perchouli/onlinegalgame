'use strict';

var Editor = React.createClass({
  displayName: 'Editor',

  getInitialState: function getInitialState() {
    return {
      scenes: [],
      editingScene: {}
    };
  },
  _editScence: function _editScence(i) {
    this.setState({
      editingScene: this.state.scenes[i]
    });
    this.props.config('sceneIndex', i);
    this.props.config('commandIndex', 0);
    this.props.config('backgroundImage', null);
    this.props.config('roles', []);
  },
  _onChangeName: function _onChangeName(e) {
    var data = this.state.editingScene;
    data['name'] = e.target.value;
    this.setState({ editingScene: data });
  },
  _renderFileBrowser: function _renderFileBrowser(category) {
    var _self = this;
    var xhr = new XMLHttpRequest();
    xhr.open('GET', '/api/' + category + '/', true);
    xhr.onreadystatechange = function () {
      if (xhr.readyState != 4 || xhr.status != 200) return;

      var DOMNode = _self.refs.fileBrowser;
      var response = JSON.parse(xhr.responseText);
      ReactDOM.unmountComponentAtNode(DOMNode);
      ReactDOM.render(React.createElement(FileBrowser, { files: response, category: category, _event: _self._selectFile }), DOMNode);
    };
    xhr.send();
  },
  _toggleBranchesEdit: function _toggleBranchesEdit(e) {
    e.preventDefault();
    var DOMNode = document.getElementById('branches-edit');
    DOMNode.style.display = DOMNode.style.display == 'block' ? 'none' : 'block';
  },
  _saveBranches: function _saveBranches(e) {
    var DOMNode = document.querySelectorAll('#branches-edit ul li'),
        branches = [];
    for (var i = 0; i < DOMNode.length; i++) {
      var li = DOMNode[i],
          label = li.querySelector('input[name="label"]').value,
          name = li.querySelector('select[name="name"]').value;
      branches.push({ label: label, name: name });
    }
    var command = '\nsp "branches",' + JSON.stringify(branches);
    this._insertCommand(command);
    this._toggleBranchesEdit(e);
  },
  _selectFile: function _selectFile(e) {
    var DOMNode = e.currentTarget,
        dataset = DOMNode.dataset,
        category = dataset['category'],
        name = dataset['name'],
        url = DOMNode.children[0].src;

    if (category == 'backgrounds') this.props.config('backgroundImage', url);
    if (category == 'roles') this.props.config('roles', [{ url: url }]);

    var command = '\nsp "' + (category == 'backgrounds' ? 'bg' : 'role') + '", {"name": "' + name + '" }\n';
    this._insertCommand(command);
  },
  _insertCommand: function _insertCommand(command) {
    var editingScene = this.state.editingScene,
        commandsString = this.refs.commandsEditor.value,
        caretPos = this._getInputSelection().end;
    commandsString = commandsString.slice(0, caretPos) + command + commandsString.slice(caretPos);

    editingScene.commands = commandsString.split('\n');
    this.setState({ editingScene: editingScene });
  },
  _editCommands: function _editCommands(e) {
    var editingScene = this.state.editingScene,
        commandsString = e.target.value;

    editingScene.commands = commandsString.split('\n');
    this.setState({ editingScene: editingScene });
  },
  _getInputSelection: function _getInputSelection(e) {
    var el = this.refs.commandsEditor,
        start = 0,
        end = 0,
        normalizedValue,
        range,
        textInputRange,
        len,
        endRange;

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
  _previewImage: function _previewImage(e) {
    var reader = new FileReader(),
        input = e.target,
        value;

    reader.onload = (function (e) {
      value = e.target.result;
      this.props.onConfig({ name: 'backgroundImage', value: 'url(' + value + ')' });
    }).bind(this);

    for (var i = 0; i < input.files.length; i++) {
      var image = input.files[i];
      reader.readAsDataURL(image);
    }
  },
  _createScene: function _createScene() {
    var scenes = this.state.scenes;
    scenes.push({ background: null, dialogs: [], name: '[NEW]' });
    this.props.config('scenes', scenes);
  },
  _saveScene: function _saveScene(e) {
    var editingScene = this.state.editingScene,
        commands = editingScene.commands,
        data = {},
        xhr = new XMLHttpRequest();
    data['commands'] = commands;
    data['name'] = editingScene.name;
    data['story_id'] = this.props.storyId;

    xhr.onreadystatechange = (function () {
      if (xhr.readyState != 4 || xhr.status != 200) return;
      var response = JSON.parse(xhr.responseText);
      if (response.length > 0) {
        editingScene.id = response[0].id;
        this.setState({ editingScene: editingScene });
      }
      alert('Success!');
    }).bind(this);

    if (editingScene.id) {
      xhr.open('PUT', '/api/scenes/' + editingScene.id + '/');
    } else {
      xhr.open('POST', '/api/scenes/');
    }
    xhr.send(JSON.stringify(data));
    e.preventDefault();
  },
  _deleteScene: function _deleteScene(e) {
    if (confirm('Delete? (cannot undo)')) {
      var scenes = this.state.scenes,
          sceneIndex = this.state.scenes.map(function (scene) {
        return scene.id;
      }).indexOf(this.state.editingScene.id);

      if (this.state.editingScene.id) {
        var xhr = new XMLHttpRequest();
        xhr.open('DELETE', '/api/scenes/' + this.state.editingScene.id + '/');
        xhr.send();
      }

      scenes.splice(sceneIndex, 1);
      this.setState({ editingScene: {} });
      this.props.config({ scenes: scenes });
    }
  },
  render: function render() {
    var configStyle = Object.keys(this.state.editingScene).length != 0 ? { display: 'inline-block' } : { display: 'none' },
        commandsString = this.state.editingScene.commands ? this.state.editingScene.commands.join('\n').replace(/&gt;/g, '>') : '';

    return React.createElement(
      'div',
      { className: 'editor fg-grid' },
      React.createElement(
        'div',
        { className: 'timeline fg-1-2' },
        this.state.scenes.map(function (scene, i) {
          return React.createElement(
            'div',
            { key: i, className: 'scene', onClick: this._editScence.bind(this, i) },
            scene.name
          );
        }, this),
        React.createElement(
          'button',
          { onClick: this._createScene, className: 'btn-new' },
          'New'
        )
      ),
      React.createElement(
        'div',
        { className: 'config fg-1-2', style: configStyle },
        React.createElement(
          'label',
          null,
          'Name: ',
          React.createElement('input', { name: '', value: this.state.editingScene.name, onChange: this._onChangeName })
        ),
        React.createElement(
          'label',
          null,
          'Scripts:',
          React.createElement(
            'div',
            { className: 'toolbar' },
            React.createElement(
              'a',
              { onClick: this._renderFileBrowser.bind(this, 'backgrounds') },
              'BGD'
            ),
            React.createElement(
              'a',
              { onClick: this._renderFileBrowser.bind(this, 'roles') },
              'Role'
            ),
            React.createElement(
              'a',
              { onClick: this._toggleBranchesEdit },
              'Branch'
            ),
            React.createElement(
              'div',
              { className: 'popup', id: 'branches-edit' },
              React.createElement(
                'ul',
                null,
                React.createElement(
                  'li',
                  null,
                  React.createElement('input', { name: 'label', placeholder: 'Label' }),
                  React.createElement(
                    'select',
                    { name: 'name' },
                    this.state.scenes.map(function (scene, i) {
                      return React.createElement(
                        'option',
                        { key: i, value: scene.name },
                        scene.name
                      );
                    }, this)
                  )
                ),
                React.createElement(
                  'li',
                  null,
                  React.createElement('input', { name: 'label', placeholder: 'Label' }),
                  React.createElement(
                    'select',
                    { name: 'name' },
                    this.state.scenes.map(function (scene, i) {
                      return React.createElement(
                        'option',
                        { key: i, value: scene.name },
                        scene.name
                      );
                    }, this)
                  )
                )
              ),
              React.createElement(
                'button',
                { onClick: this._saveBranches },
                'Save'
              )
            )
          ),
          React.createElement('textarea', { ref: 'commandsEditor', className: 'commands', onChange: this._editCommands, value: commandsString }),
          React.createElement(
            'button',
            { onClick: this._saveScene },
            'Save'
          ),
          React.createElement(
            'button',
            { onClick: this._deleteScene },
            'Delete'
          )
        )
      ),
      React.createElement('div', { ref: 'fileBrowser' })
    );
  }
});

var FileBrowser = React.createClass({
  displayName: 'FileBrowser',

  render: function render() {
    return React.createElement(
      'div',
      { className: 'file-browser' },
      React.createElement(
        'a',
        { className: 'close', onClick: (function () {
            ReactDOM.unmountComponentAtNode(React.findDOMNode(this).parentNode);
          }).bind(this) },
        'x'
      ),
      this.props.files.map(function (file, i) {
        var style = file.isSelected ? { 'backgroundColor': '#000' } : {};
        return React.createElement(
          'a',
          { key: i, 'data-name': file.name, 'data-category': this.props.category, className: 'fg-1-12', style: style, onClick: this.props._event },
          React.createElement('img', { className: 'item', src: file.image }),
          React.createElement(
            'h4',
            null,
            file.name
          )
        );
      }, this)
    );
  }
});

var Branches = React.createClass({
  displayName: 'Branches',

  render: function render() {
    return React.createElement(
      'div',
      null,
      this.props.branches.map(function (branch, i) {
        return React.createElement(
          'a',
          { key: i, 'data-name': branch.name, className: 'branch', onClick: this.props._event },
          branch.label
        );
      }, this)
    );
  }
});

var MainWindow = React.createClass({
  displayName: 'MainWindow',

  getInitialState: function getInitialState() {
    return {
      scenes: [],
      sceneIndex: 0,
      commandIndex: 0,
      isEditing: false,
      roles: [],
      backgroundImage: null
    };
  },
  componentDidMount: function componentDidMount() {
    if (this.props.storyId) {
      var xhr = new XMLHttpRequest();
      xhr.open('GET', '/api/stories/' + this.props.storyId + '/', true);
      xhr.onreadystatechange = (function () {
        if (xhr.readyState != 4 || xhr.status != 200) return;
        var response = JSON.parse(xhr.responseText),
            scenes = response.scenes;

        this.setState({ scenes: response.scenes });
        scenes = response.scenes;
      }).bind(this);
      xhr.send();
    }
  },
  componentDidUpdate: function componentDidUpdate() {
    this.refs.editor.setState({ scenes: this.state.scenes });
  },
  _config: function _config(name, value) {
    this.state[name] = value;
    this.forceUpdate();
  },
  _toggleEditor: function _toggleEditor(e) {
    if (this.props.mountEditor) {
      e.preventDefault();
      var DOMNode = ReactDOM.findDOMNode(this.refs.editor);

      if (DOMNode.style.display == '' || DOMNode.style.display == 'none') DOMNode.style.display = 'block';else DOMNode.style.display = 'none';

      this.setState({ isEditing: !this.state.isEditing });
    }
  },
  _getURLAsName: function _getURLAsName(category, name, callback) {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', '/api/' + category + '/?name=' + name, true);
    xhr.onreadystatechange = function () {
      if (xhr.readyState != 4 || xhr.status != 200) return;
      var response = JSON.parse(xhr.responseText)[0];
      callback(response.image);
    };
    xhr.send();
  },
  _setBackgroundImage: function _setBackgroundImage(args) {
    if (args === null) this.setState({ backgroundImage: null });else {
      this._getURLAsName('backgrounds', args.name, (function (url) {
        this.setState({ backgroundImage: url });
      }).bind(this));
    }
  },
  _setRole: function _setRole(args, isDelete) {
    var roles = this.state.roles;
    if (isDelete) {
      var roleIndex = roles.map(function (role) {
        return role.name;
      }).indexOf(args.name);
      if (roleIndex !== -1) roles.splice(roleIndex, 1);
      this.setState({ roles: roles });
    } else this._getURLAsName('roles', args.name, (function (url) {
      roles.push({
        name: args.name,
        url: url,
        style: args
      });
      this.setState({ roles: roles });
    }).bind(this));
  },
  _goto: function _goto(e) {
    var name = e.currentTarget.dataset.name,
        sceneIndex = this.state.scenes.map(function (scene) {
      return scene.name;
    }).indexOf(name);
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
  _play: function _play(e) {
    if (this.state.isEditing === true || this.state.scenes[this.state.sceneIndex] === undefined || this.refs.dialogBox.children.length !== 0) return e.preventDefault();

    if (this.state.sceneIndex === this.state.scenes.length - 1 && this.state.commandIndex === this.state.scenes[this.state.sceneIndex].commands.length) {
      alert('Ending');
      return e.preventDefault();
    }

    var scene = this.state.scenes[this.state.sceneIndex],
        command = scene.commands[this.state.commandIndex];

    if (command === '') {
      this.setState({ commandIndex: this.state.commandIndex + 1 }, (function () {
        ReactDOM.findDOMNode(this).click();
      }).bind(this));
      //return e.preventDefault();
    }

    if (command !== undefined) {
      var commaFirstPos = command.indexOf(',');
      if (command.substr(0, 4) == '&gt;') ReactDOM.findDOMNode(this.refs.dialogBox).innerHTML = command.substr(4);else if (commaFirstPos === -1) {
        switch (command) {
          case 'spdelete "bg"':
            this._setBackgroundImage(null);
            break;
        }
      } else {
        var method = command.substr(0, command.indexOf(',')).trim(),
            args = command.substr(command.indexOf(',') + 1).trim();

        try {
          args = JSON.parse(args);
        } catch (e) {
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
            ReactDOM.render(React.createElement(Branches, { branches: args, _event: this._goto }), DOMNode);
            break;
        }
      }
    }

    if (this.state.commandIndex <= scene.commands.length - 1) this.setState({ commandIndex: this.state.commandIndex + 1 });else {
      if (this.state.sceneIndex !== this.state.scenes.length - 1) this.setState({
        commandIndex: 0,
        sceneIndex: this.state.sceneIndex + 1
      });
    }
  },
  render: function render() {
    var mainWindowStyle = {};
    mainWindowStyle['backgroundImage'] = this.state.backgroundImage ? 'url(' + this.state.backgroundImage + ')' : null;
    return React.createElement(
      'div',
      { className: 'main-window', onClick: this._play, onContextMenu: this._toggleEditor, style: mainWindowStyle },
      this.state.roles.map(function (role, i) {
        var style = role.style || {};
        style['backgroundImage'] = 'url(' + role.url + ')';
        return React.createElement('div', { key: i, className: 'role', style: style });
      }, this),
      React.createElement('div', { ref: 'dialogBox', className: 'dialog-box' }),
      React.createElement(Editor, { ref: 'editor', config: this._config, storyId: this.props.storyId })
    );
  }
});
