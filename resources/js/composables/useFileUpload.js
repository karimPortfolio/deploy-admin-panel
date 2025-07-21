import { ref } from "vue";

export function useFileUpload(target, property = "image") {
  const image = ref(null);

  const input = document.createElement("input");

  input.type = "file";

  input.onchange = (e) => {
    onChange(e);
  };

  function openFileSelector() {
    input.click();
  }

  function onChange(e) {
    target.value[property] = e.target.files[0];

    const reader = new FileReader();

    reader.onload = (e) => {
      image.value = e.target.result;
    };

    reader.readAsDataURL(e.target.files[0]);
  }

  return {
    image,
    openFileSelector,
    onChange,
  };
}