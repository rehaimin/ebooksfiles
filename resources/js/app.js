import "./bootstrap";
import * as FilePond from "filepond";

// Create a multi file upload component
const pond = FilePond.create({
    multiple: true,
    name: "filepond",
});

// Add it to the DOM
// document.body.appendChild(pond.element);

FilePond.parse(document.body);

FilePond.setOptions({
    server: {
        url: "/filepond/api",
        process: "/process",
        revert: "/process",
        patch: "?patch=",
        headers: {
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
        },
    },
});
