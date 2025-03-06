<template>
    <a href="/receptes">
      <button class="back-btn">
        <slot>Atpakaļ</slot>
      </button>
    </a>
  </template>
  
  <script>
  export default {
    name: 'BackButton'
  }
  </script>
  
  <style scoped>
  .back-btn {
    padding: 1rem 3rem;
    font-size: 1rem;
    border: none;
    color: white;
    background: black;
    cursor: pointer;
    border-radius: 50px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }
  
  .back-btn:hover {
    background-color: dimgrey;
  }
  </style>