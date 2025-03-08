document.addEventListener("livewire:init", () => {
  Livewire.on("file-upload", event => {
    Livewire.dispatch("loader", {
      show: true
    });

    var formData = new FormData();
    var file = $("#file")[0].files[0];
    formData.append("file", file);

    $.ajax({
      url: "/upload-file",
      type: "POST",
      headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
      },
      data: formData,
      processData: false,
      contentType: false,
      success: function(response) {
        Livewire.dispatch("store", {
          file: response
        });
      }
    });
  });

  Livewire.on("reload", () => {
    Livewire.navigate(window.location.href);
  });

  Livewire.on("open-square-app", event => {
    url = event.url;
    // console.log(typeof event.url);
    setTimeout(() => {
      // window.location.href = event.url;
      window.open(url);
  }, 1000);
    console.log(url);
  });
});
