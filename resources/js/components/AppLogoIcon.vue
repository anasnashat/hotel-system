<template>
  <div class="app-logo">
    <a href="/">
      <!-- You can use an image logo or a text logo -->
      <!-- <img src="/images/logo.png" alt="App Logo" class="logo" /> -->
      <!-- Alternatively, use text for the logo -->
      <!-- <span class="logo-text">MyApp</span> -->
    </a>
  </div>
</template>

<script>
export default {
  name: 'AppLogo',
};
</script>

<style scoped>
.app-logo {
  display: flex;
  align-items: center;
  justify-content: center;
}

.logo {
  width: 100px;  /* Adjust size */
  height: auto;
}

.logo-text {
  font-size: 24px;
  font-weight: bold;
  color: #333;
  text-decoration: none;
}
</style>
