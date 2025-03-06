<template>
    <div class="instructions">
      <h2>Kā pagatavot:</h2>
      <ul>
        <li v-for="(instruction, index) in instructions" :key="index">
          {{ instruction.apraksts }}
        </li>
      </ul>
    </div>
  </template>
  
  <script>
  export default {
    name: 'RecDynInstructions',
    props: {
      instructions: Array
    }
  }
  </script>

<style>
    li {
    font-size: 16px;
    margin-bottom: 5px;
  }
</style>
