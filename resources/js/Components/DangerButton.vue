<template>
    <button class="danger-button">
      <slot />
    </button>
  </template>
  
  <style scoped>
  .danger-button {
    display: inline-flex;
    align-items: center;
    padding: 0.4rem 0.9rem;
    font-size: 0.75rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    font-family: monospace;
    color: #fff;
    background-color: #a94442; /* softer red */
    border: 1px solid #a94442;
    border-radius: 4px;
    transition: background-color 0.2s ease, box-shadow 0.2s ease;
    cursor: pointer;
  }
  
  .danger-button:hover {
    background-color: #c44b48; /* hover tone */
  }
  
  .danger-button:active {
    background-color: #843534; /* deeper tone on click */
  }
  
  .danger-button:focus {
    outline: none;
    box-shadow: 0 0 0 2px rgba(169, 68, 66, 0.5);
  }
  </style>
  