<template>
  <div>
    <iframe :src="currentFileUrl" width="100%" height="500"></iframe>
    <input v-model="filename" placeholder="New filename" />
    <input v-model="destination" placeholder="Destination folder" />
    <button @click="renameAndMove">Rename & Move</button>
    <button @click="prevFile">Prev</button>
    <button @click="nextFile">Next</button>
  </div>
</template>

<script>
export default {
  data() {
    return {
      files: [],
      currentIndex: 0,
      filename: '',
      destination: ''
    };
  },
  computed: {
    currentFile() {
      return this.files[this.currentIndex] || {};
    },
    currentFileUrl() {
      return '/remote.php/webdav' + this.currentFile.path;
    }
  },
  methods: {
    loadFiles() {
      fetch(OC.generateUrl('/apps/nextcloudscanworkflow/files'))
        .then(res => res.json())
        .then(data => {
          this.files = data;
          this.filename = this.currentFile.name;
        });
    },
    nextFile() {
      if (this.currentIndex < this.files.length - 1) {
        this.currentIndex++;
        this.filename = this.currentFile.name;
      }
    },
    prevFile() {
      if (this.currentIndex > 0) {
        this.currentIndex--;
        this.filename = this.currentFile.name;
      }
    },
    renameAndMove() {
      alert('Rename & move: ' + this.filename + ' to ' + this.destination);
    }
  },
  mounted() {
    this.loadFiles();
  }
};
</script>
