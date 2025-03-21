<template>
  <div class="nav-user">
    <div class="user-info">
      <span class="username">{{ user.name }}</span>
      <button @click="toggleDropdown" class="dropdown-toggle">
        <i class="icon fa fa-caret-down"></i>
      </button>
    </div>

    <!-- Dropdown menu (optional) -->
    <div v-if="dropdownOpen" class="dropdown-menu">
      <ul>
        <li><a href="/profile">Profile</a></li>
        <li><a href="/settings">Settings</a></li>
        <li><a href="#" @click="logout">Logout</a></li>
      </ul>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      dropdownOpen: false,
      user: {
        name: 'John Doe', // This can be fetched from Laravel
      },
    };
  },
  methods: {
    toggleDropdown() {
      this.dropdownOpen = !this.dropdownOpen;
    },
    logout() {
      // Handle logout (can use Inertia to redirect to the logout route)
      this.$inertia.post('/logout');
    },
  },
};
</script>

<style scoped>
.nav-user {
  display: flex;
  align-items: center;
  justify-content: flex-end;
}

.user-info {
  margin-right: 20px;
  display: flex;
  align-items: center;
}

.dropdown-menu {
  position: absolute;
  background: #fff;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  border-radius: 4px;
  top: 40px;
  right: 10px;
  width: 200px;
}

.dropdown-menu ul {
  list-style-type: none;
  padding: 0;
}

.dropdown-menu li {
  padding: 10px;
}

.dropdown-menu li a {
  text-decoration: none;
  color: #333;
}
</style>
