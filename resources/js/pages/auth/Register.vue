<script setup>
import { ref, onMounted  } from "vue";
import { useForm } from "@inertiajs/vue3";
import { Input } from "@/components/ui/input";
import { Button } from "@/components/ui/button";
import NavMain from '@/components/NavMain.vue';
import { Card, CardHeader, CardContent } from "@/components/ui/card";
import { Label } from "@/components/ui/label";
import axios from "axios";
import NavBarMain from "@/components/NavBarMain.vue";

const countries = ref([]); // Store country data

// Define a reactive reference to track the current step of the registration proces
const step = ref(1);
// Checkbox States
const termsAccepted = ref(false);
const honestyConfirmed = ref(false);

// Create a form object with initial empty values for user registration
const form = useForm({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
    gender: "",
    national_id: "",
    country: "",
    country_code: "",
    phone_number: "",
    avatar: null,
});

/**
 * Function to check if a given field value (e.g., email, national ID, phone number)
 * already exists in the system to prevent duplicates.
 *
 * */
const checkExistence = async (field, value) => {
    try {
        const response = await axios.post("/check-existence", { field, value });
        return response.data.exists;
    } catch (error) {
        console.error(`Error checking ${field}:`, error);
        return false;
    }
};

//Function To Go to the next Step for the user to registor more information
const nextStep = async () => {
    form.clearErrors();
    // Validate fields before moving forward
    if(step.value===1 && /\d/.test(form.name) ) return form.setError("name", "Name should not contain numbers.");
    if (step.value === 1 && !form.name) return form.setError("name", "Name is required");
    if (step.value === 1 && !form.email) return form.setError("email", "Email is required");
    if (step.value === 1 && !form.password) return form.setError("password", "Password is required");
    if (step.value === 1 && form.password !== form.password_confirmation) return form.setError("password_confirmation", "Passwords do not match");
    if (step.value === 1 && form.password.length < 8) {
        return form.setError("password", "Password must be at least 8 characters.");
    }
    if (step.value === 1 && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) {
        return form.setError("email", "Enter a valid email address");
    }
    const emailExists = await checkExistence("email", form.email);
    if (emailExists) return form.setError("email", "Email is already registered");

    if(step.value===2 && !/^\d+$/.test(form.national_id)) return form.setError("national_id", "National ID should contain only digits.");
    if (step.value === 2 && !form.gender) return form.setError("gender", "Gender is required");
    if (step.value === 2 && !form.national_id) return form.setError("national_id", "National ID is required");
    if (step.value === 2 && !form.country) return form.setError("country", "Country is required");
    if (step.value === 2 && !form.phone_number) return form.setError("phone_number", "Phone Number is required");
    if(step.value===2 && !/^\d+$/.test(form.phone_number)) return form.setError("phone_number", "Phone number should contain only digits.");
    const nationalIdExists = await checkExistence("national_id", form.national_id);
    if (nationalIdExists) return form.setError("national_id", "National ID is already registered");

    const phoneExists = await checkExistence("phone_number", form.phone_number);
    if (phoneExists) return form.setError("phone_number", "Phone number is already registered");

    step.value++;
};

// Function to go back to the previous step in the registration form
const prevStep = () => step.value--;

//Function to submit the registration form.
const submit = () => {
    if (!termsAccepted.value || !honestyConfirmed.value) return;
    form.post(route("register"), {
        forceFormData: true,
        onError: (errors) => {
            if (errors.email) {
                form.setError("email", errors.email);
            }
            console.log("Validation errors:", errors);
        },
    });
};

// Fetch countries from the API when the component is mounted
onMounted(async () => {
    try {
        const response = await axios.get("/countries");
        console.log(response.data);
        if (response.data && typeof response.data === "object") {
            countries.value = response.data; // Ensure it's an object
        } else {
            console.error("Invalid countries data format:", response.data);
            countries.value = {};
        }
    } catch (error) {
        console.error("Error fetching countries:", error);
    }
});

//to update automatically country code field
const updateCountryCode = () => {
    if (!countries.value) return;
    const selectedEntry = Object.entries(countries.value).find(([code, data]) => data.name === form.country);
    form.country_code = selectedEntry ? selectedEntry[0] : "";
};

</script>

<template>
    <div class=" flex-col">
    <NavBarMain/>
    <Card class="max-w-lg mx-auto p-6 shadow-lg mt-5">
        <CardHeader class="text-center">
            <h2 class="text-xl font-bold">Register</h2>
        </CardHeader>

        <CardContent>
            <!-- Step 1: Basic Info -->
            <div v-if="step === 1">
                <Label>Name</Label>
                <Input v-model="form.name" placeholder="Full Name" class="mb-2 mt-1" />
                <p v-if="form.errors.name" class="text-red-500 text-sm mb-2">{{ form.errors.name }}</p>

                <Label >Email</Label>
                <Input type="email" v-model="form.email" placeholder="Email Address" class="mb-2 mt-1" />
                <p v-if="form.errors.email" class="text-red-500 text-sm mb-2">{{ form.errors.email }}</p>

                <Label>Password</Label>
                <Input type="password" v-model="form.password" placeholder="Password" class="mb-2 mt-1" />
                <p v-if="form.errors.password" class="text-red-500 text-sm mb-2">{{ form.errors.password }}</p>

                <Label>Confirm Password</Label>
                <Input type="password" v-model="form.password_confirmation" placeholder="Confirm Password" class="mb-2 mt-1" />
                <p v-if="form.errors.password_confirmation" class="text-red-500 text-sm mb-2">{{ form.errors.password_confirmation }}</p>

                <Button @click="nextStep" class="bg-[#5b5329] hover:bg-[#FFFFFF] hover:text-black hover:border-2 hover:border-black text-white mt-4 w-full">Next</Button>
            </div>

            <!-- Step 2: Personal Details -->
            <div v-if="step === 2">

                <div class="space-y-2 mb-2 mt-2 ms-2">
                    <Label>Gender</Label>
                    <div class="flex items-center space-x-3 cursor-pointer">
                        <input type="radio" id="male" value="Male" v-model="form.gender" class="cursor-pointer" />
                        <label for="male">Male</label>
                    </div>
                    <div class="flex items-center space-x-3 cursor-pointer">
                        <input type="radio" id="female" value="Female" v-model="form.gender" class="cursor-pointer" />
                        <label for="female">Female</label>
                    </div>
                </div>
                <p v-if="form.errors.gender" class="text-red-500 text-sm mb-2">{{ form.errors.gender }}</p>

                <Label>National ID</Label>
                <Input v-model="form.national_id" placeholder="National ID" class="mb-2 mt-1" />
                <p v-if="form.errors.national_id" class="text-red-500 text-sm mb-2">{{ form.errors.national_id }}</p>

                <Label>Country</Label>
                <select v-model="form.country" @change="updateCountryCode" class="mb-2 mt-1 border p-2 w-full text-black">
                <option value="" disabled>Select your country</option>
                <option v-for="country in countries" :key="country.cca2" :value="country.name">
                {{ country.name }}
            </option>
            </select>
            <p v-if="form.errors.country" class="text-red-500 text-sm mb-2">{{ form.errors.country }}</p>

                <Label>Country Code</Label>
                <Input v-model="form.country_code" placeholder="Country Code" class="mt-4" disabled/>

                <Label>Phone Number</Label>
                <Input v-model="form.phone_number" placeholder="Phone Number" class="mb-2 mt-1" />
                <p v-if="form.errors.phone_number" class="text-red-500 text-sm mb-2">{{ form.errors.phone_number }}</p>
                <div class="flex justify-between mt-4">
                    <Button variant="outline" @click="prevStep">Back</Button>
                    <Button @click="nextStep" class="bg-[#5b5329] hover:bg-[#FFFFFF] hover:text-black hover:border-2 hover:border-black  text-white ">Next</Button>
                </div>
            </div>
                <!-- Step 3: Upload Avatar -->
            <div v-if="step === 3">
                <Label>Profile Picture</Label>
                <Input type="file" @change="(e) => form.avatar = e.target.files[0]" class="mb-4 mt-1"/>

             <!-- Checkboxes -->
             <div class="mt-4">
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" v-model="termsAccepted" class="form-checkbox text-[#5b5329]" />
                        <span>I accept the terms and conditions.</span>
                    </label>
                    <label class="flex items-center space-x-2 mt-2">
                        <input type="checkbox" v-model="honestyConfirmed" class="form-checkbox text-[#5b5329]" />
                        <span>I confirm that the provided information is true.</span>
                    </label>
            </div>

                    <div class="flex justify-between mt-4">
                        <Button variant="outline" @click="prevStep">Back</Button>
                        <Button @click="submit" :disabled="!termsAccepted || !honestyConfirmed" class="bg-[#5b5329] text-white disabled:opacity-50">Register</Button>
                    </div>

            </div>
        </CardContent>
    </Card>
</div>
</template>
