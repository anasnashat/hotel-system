<script setup>
import { ref } from "vue";
import { router, useForm } from "@inertiajs/vue3";
import { Input } from "@/components/ui/input";
import { Button } from "@/components/ui/button";
import { Label } from "@/components/ui/label";
import { Card, CardHeader, CardContent } from "@/components/ui/card";
import { Checkbox } from "@/components/ui/checkbox";
import NavBarMain from "@/components/NavBarMain.vue";

const form = useForm({
  email: "",
  password: "",
  remember: false,
});

const submit = () => {
  form.post("/login");
};
</script>

<template>
  <NavBarMain/>

  <div class="flex justify-center mt-5">
    <Card class="w-full max-w-lg p-6 shadow-lg">
      <CardHeader class="text-center">
        <h2 class="text-xl font-bold">Login</h2>
      </CardHeader>

      <CardContent>
        <form @submit.prevent="submit" class="space-y-4">
          <div>
            <Label for="email">Email</Label>
            <Input id="email" v-model="form.email" type="email" placeholder="Enter your email" class="mt-1" />
            <p v-if="form.errors.email" class="text-red-500 text-sm">{{ form.errors.email }}</p>
          </div>

          <div>
            <Label for="password">Password</Label>
            <Input id="password" v-model="form.password" type="password" placeholder="Enter your password" class="mt-1" />
            <p v-if="form.errors.password" class="text-red-500 text-sm">{{ form.errors.password }}</p>
          </div>

          <div class="flex items-center space-x-2">
            <Checkbox id="remember" v-model="form.remember" />
            <Label for="remember">Remember Me</Label>
          </div>

          <Button type="submit" class="w-full bg-[#5b5329] hover:bg-[#FFFFFF] hover:text-black hover:border-2 hover:border-black text-white" :disabled="form.processing">
            Login
          </Button>
        </form>
      </CardContent>
    </Card>
  </div>
  
</template>
