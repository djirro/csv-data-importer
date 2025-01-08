<template>
  <div
    class="drop-zone"
    :class="{ dragging: isDragging }"
    @dragover.prevent="onDragOver"
    @dragleave="onDragLeave"
    @drop.prevent="onDrop"
  >
    <p v-if="errorMessage" class="error">{{ errorMessage }}</p>
    <p>Перетащите файл сюда или выберите его</p>
    <input
      type="file"
      accept=".csv"
      @change="onFileSelect"
      hidden
      ref="fileInput"
    />
    <button @click="triggerFileSelect">Выбрать файл</button>
  </div>
</template>

<script>
export default {
  name: "DragAndDrop",
  data() {
    return {
      isDragging: false,
      errorMessage: "",
    };
  },
  methods: {
    onDragOver(event) {
      this.isDragging = true;
    },

    onDragLeave() {
      this.isDragging = false;
    },

    onDrop(event) {
      this.isDragging = false;
      const file = event.dataTransfer.files[0];
      this.validateFile(file);
    },

    onFileSelect(event) {
      const file = event.target.files[0];
      this.validateFile(file);
    },

    triggerFileSelect() {
      this.$refs.fileInput.click();
    },

    validateFile(file) {
      if (!file) {
        this.showErrorMessage("Файл не выбран.");
        return;
      }
      if (file.type !== "text/csv") {
        this.showErrorMessage("Неверный формат файла. Выберите .csv файл.");
        return;
      }
      this.errorMessage = "";
      console.log("Файл принят:", file);
      this.uploadFile(file);
    },

    async uploadFile(file) {
      const formData = new FormData();
      formData.append("file", file);

      try {
        const response = await fetch("http://localhost:8080/upload", {
          method: "POST",
          body: formData,
        });

        const result = await response.text();
        console.log(result); 

        if (response.ok) {
          this.$emit("upload-success", result);
          alert("Файл успешно загружен!");
        } else {
          this.$emit("upload-error", result);
          this.errorMessage = `Ошибка: ${result}`;
        }
      } catch (err) {
        this.$emit("upload-error", "Ошибка соединения с сервером");
        this.errorMessage = "Ошибка соединения с сервером.";
      }
    },

    showErrorMessage(message) {
      this.errorMessage = message;

      setTimeout(() => {
        this.errorMessage = "";
      }, 5000);
    },
  },
};
</script>

<style scoped>
.drop-zone {
  width: 100%;
  height: 600px;
  border: 2px dashed #ccc;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  margin: 25px auto;
  transition: background-color 0.3s, color 0.3s;
  cursor: pointer;
}

.drop-zone.dragging {
  background-color: #f0f8ff;
  border-color: #42b983;
}

button {
  margin-top: 10px;
  padding: 8px 16px;
  background-color: #42b983;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

button:hover {
  background-color: #369971;
}

.error {
  color: red;
  font-size: 14px;
  margin-bottom: 10px;
}
</style>