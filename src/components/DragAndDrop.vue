<template>
  <!-- Основная зона для перетаскивания и выбора файла. -->
  <div
    class="drop-zone"
    :class="{ dragging: isDragging }"
    @dragover.prevent="onDragOver"
    @dragleave="onDragLeave"
    @drop.prevent="onDrop"
  >
    <!-- Спиннер отображается во время загрузки файла. -->
    <div v-if="isLoading" class="spinner-container">
      <span class="spinner">⏳</span> Загрузка данных...
    </div>

    <!-- Основной контент, если файл не загружается. -->
    <div v-else>
      <!-- Сообщение об ошибке -->
      <p v-if="errorMessage" class="error">{{ errorMessage }}</p>
      <p>Перетащите файл сюда или выберите его</p>
      <!-- Скрытый input для выбора файла. -->
      <input
        type="file"
        accept=".csv"
        @change="onFileSelect"
        hidden
        ref="fileInput"
      />
      <!-- Кнопка для вызова выбора файла. -->
      <button @click="triggerFileSelect">Выбрать файл</button>
    </div>
  </div>
</template>

<script>
export default {
  name: "DragAndDrop",
  data() {
    return {
      isDragging: false,
      errorMessage: "",
      isLoading: false,
    };
  },
  methods: {
    // Устанавливает состояние перетаскивания в true.
    onDragOver(event) {
      this.isDragging = true;
    },

    // Сбрасывает состояние перетаскивания.
    onDragLeave() {
      this.isDragging = false;
    },

    // Обрабатывает перетаскивание файла.
    onDrop(event) {
      this.isDragging = false;
      const file = event.dataTransfer.files[0];
      this.validateFile(file);
    },

    // Обрабатывает выбор файла через input.
    onFileSelect(event) {
      const file = event.target.files[0];
      this.validateFile(file);
    },

    // Открывает диалоговое окно для выбора файла.
    triggerFileSelect() {
      this.$refs.fileInput.click();
    },

    // Проверяет файл на корректность (наличие и формат).
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

    // Загружает файл на сервер и обрабатывает ответ.
    async uploadFile(file) {
      const formData = new FormData();
      formData.append("file", file);

      this.isLoading = true;

      try {
        const response = await fetch("http://localhost:8080/upload", {
          method: "POST",
          body: formData,
        });

        if (response.ok) {
          await this.downloadFile(response);
          this.isLoading = false;
          this.$emit("upload-success", "Файл успешно загружен!");
          alert("Файл успешно загружен!");
        } else {
          const result = await response.text();
          this.isLoading = false;
          this.$emit("upload-error", result);
          this.errorMessage = `Ошибка: ${result}`;
        }
      } catch (err) {
        this.isLoading = false;
        this.$emit("upload-error", "Ошибка соединения с сервером");
        this.errorMessage = "Ошибка соединения с сервером.";
      }
    },

    // Скачивает отчет об ошибках, если файл содержит некорректные данные.
    async downloadFile(response) {
      const contentDisposition = response.headers.get("Content-Disposition");
      console.log("Content-Disposition header:", contentDisposition);

      let fileName = "error_report.csv";

      if (contentDisposition) {
        const matches = contentDisposition.match(
          /filename\*?=\s*["']?([^"';\n]+)/
        );
        console.log("Matches:", matches);

        if (matches && matches[1]) {
          fileName = matches[1];
        }
      }

      const blob = await response.blob();
      const downloadUrl = window.URL.createObjectURL(blob);
      const a = document.createElement("a");
      a.href = downloadUrl;
      a.download = fileName;
      a.click();

      window.URL.revokeObjectURL(downloadUrl);
    },

    // Отображает сообщение об ошибке на 5 секунд.
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
/* Стили для зоны перетаскивания. */
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

/* Состояние перетаскивания. */
.drop-zone.dragging {
  background-color: #f0f8ff;
  border-color: #42b983;
}

/* Кнопка для выбора файла. */
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

/* Сообщение об ошибке. */
.error {
  color: red;
  font-size: 14px;
  margin-bottom: 10px;
}

/* Спиннер. */
.spinner-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}

.spinner {
  font-size: 30px;
  animation: spin 1s infinite linear;
}

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}
</style>