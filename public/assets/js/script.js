const API_URL = 'http://localhost:8000/nodes';

intervals = [];

window.onload = function (ev) {
    fetchNodes();
}

function fetchNodes(rotatedTillId = 0) {
    fetch(API_URL, {
        method: "GET",
        headers: new Headers({
            'Content-Type': 'application/json'
        })
    })
    .then(response => response.json())
    .then(function (response) {
        if (Object.keys(response).length) {
            document.getElementById('nodes').innerHTML = buildNodes([response], rotatedTillId);
        } else {
            document.getElementById('nodes').innerHTML =
                '<button onclick="loadModal(0)" type="button" class="btn btn-primary" data-toggle="modal" data-target="#createNodeModal">' +
                'CREATE ROOT NODE' +
                '</button>';
        }
    })
}

function createNode() {
    let title = document.getElementById('title').value;

    let id = this.id;

    let data = {
        "parent_id": this.id,
        "title": title
    };

    if (!title || !title.length) {
        alert('Title cannot be empty');
    } else {
        fetch(API_URL, {
            method: "POST",
            headers: new Headers({
                'Content-Type': 'application/json'
            }),
            body: JSON.stringify(data)
        })
            .then(function (response) {
                if (response.status === 200) {
                    $('#createNodeModal').modal('hide')
                    fetchNodes(id);
                }
            });
    }
}

function updateNode() {
    let title = this.title;
    let id = this.id;

    let data = {
        "title": document.getElementById('nodeTitle').value
    };

    if (!title || !title.length) {
        alert('Title cannot be empty');
    } else {
        fetch(API_URL + '/' + id, {
            method: "PUT",
            headers: new Headers({
                'Content-Type': 'application/json'
            }),
            body: JSON.stringify(data)
        })
            .then(function (response) {
                if (response.status === 201) {
                    $('#updateNodeModal').modal('hide')
                    fetchNodes(id);
                }
            });
    }
}

function deleteNode() {
    let id = this.id;

    fetch(API_URL + '/' + id, {
        method: "DELETE",
        headers: new Headers({
            'Content-Type': 'application/json'
        }),
    })
        .then(function (response) {
            if (response.status === 201) {
                $('#deleteNodeModal').modal('hide')
                fetchNodes(id);
            }
        });
}

function loadModal(id, isDeleteModal = false, title = '') {
    this.id = id;
    this.title = title;

    document.getElementById("nodeTitle").value = title.toString();
    document.getElementById("title").value = "";

    if (isDeleteModal) {

        this.intervals.forEach(clearInterval)

        let current = document.getElementById('countdown');

        current.innerHTML = "20";

        let x = setInterval(function() {
            let newValue = (parseInt(current.innerHTML) - 1);

            if (newValue === 0) {
                clearInterval(x);
                $('#deleteNodeModal').modal('hide');
            }

            current.innerHTML = newValue.toString();
        }, 1000);

        this.intervals.push(x);
    }
}

const buildNodes = (nodes, rotatedTillId = 0) =>
    `<ul>${nodes.map(
        ({id, parent_id, title, children}) => children
            ? (
                '<li class="item deeper parent"> ' +
                '<a class="" href="#"> ' +
                '<span data-toggle="collapse" data-parent="#menu-group-"' + parent_id + ' href="#sub-item-' + id + '" class="arrow">' +
                '<i class="icon-play ' + (rotatedTillId ? "rotated " : "") + '"></i></span> ' +
                '<span onclick="loadModal(' + id + ', true' + ', \'' + title + '\'' +')" class="lbl" data-toggle="modal" data-target="#updateNodeModal">' + title + '</span></a>' +
                '<button onclick="loadModal(' + id + ')" type="button" class="btn btn-primary" data-toggle="modal" data-target="#createNodeModal">+</button>' +
                '<button onclick="loadModal(' + id + ', ' + true + ')" type="button" class="btn btn-primary" data-toggle="modal" data-target="#deleteNodeModal">-</button>' +
                '<ul class="children nav-child unstyled small collapse" id="sub-item-' + id + '" ' + (rotatedTillId ? " style=\"height: auto;\" " : "") + '>' +
                buildNodes(children, rotatedTillId) +
                '</ul>' +
                '</li>'
            )
            : (
                '<li class="item deeper parent"> ' +
                '<a class="" href="#"> ' +
                '<span data-toggle="collapse" data-parent="#menu-group-' + parent_id + '" class="arrow"><i ' +
                'class="icon-play hidden"></i></span> ' +
                '<span class="lbl">' + title + '</span> ' +
                '</a>' +
                '<button type="button" onclick="loadModal(' + id + ')" class="btn btn-primary" data-toggle="modal" data-target="#createNodeModal">+</button>' +
                '<button type="button" onclick="loadModal(' + id + ', ' + true + ')" class="btn btn-primary" data-toggle="modal" data-target="#deleteNodeModal">-</button>' +
                '</li>'
            )
    ).join('')}</ul>`