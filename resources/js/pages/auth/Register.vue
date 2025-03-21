<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import { Input } from "@/components/ui/input";
import { Button } from "@/components/ui/button";
import NavMain from '@/components/NavMain.vue';
import { Card, CardHeader, CardContent } from "@/components/ui/card";
import { Label } from "@/components/ui/label";
import { RadioGroup, RadioGroupItem } from "@/components/ui/radio-group";
import axios from "axios";



// Define a reactive reference to track the current step of the registration proces
const step = ref(1);

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

    if (step.value === 2 && !form.gender) return form.setError("gender", "Gender is required");
    if (step.value === 2 && !form.national_id) return form.setError("national_id", "National ID is required");
    if (step.value === 2 && !form.country) return form.setError("country", "Country is required");
    if (step.value === 2 && !form.phone_number) return form.setError("phone_number", "Phone Number is required");
    
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
</script>

<template>
    <div class=" flex-col">
    <NavMain />
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
                <Label>Gender</Label>
                <RadioGroup v-model="form.gender" class="space-y-2 mb-2 mt-2 ms-2">
                    <label class="flex items-center space-x-3 cursor-pointer">
                        <RadioGroupItem value="Male" id="male" />
                        <span>Male</span>
                    </label>
                    <label class="flex items-center space-x-3 cursor-pointer">
                        <RadioGroupItem value="Female" id="female" />
                        <span>Female</span>
                    </label>
                </RadioGroup>
                <p v-if="form.errors.gender" class="text-red-500 text-sm mb-2">{{ form.errors.gender }}</p>

                <Label>National ID</Label>
                <Input v-model="form.national_id" placeholder="National ID" class="mb-2 mt-1" />
                <p v-if="form.errors.national_id" class="text-red-500 text-sm mb-2">{{ form.errors.national_id }}</p>

                <Label>Country</Label>
                <Input v-model="form.country" placeholder="Country" class="mb-2 mt-1" />
                <p v-if="form.errors.country" class="text-red-500 text-sm mb-2">{{ form.errors.country }}</p>

                <Label>Country Code</Label>
                <Input v-model="form.country_code" placeholder="Country Code (e.g., US, EG)" class="mt-4" />

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
                
                <div class="flex justify-between mt-4">
                    <Button variant="outline" @click="prevStep">Back</Button>
                    <Button @click="submit" class="bg-[#5b5329] hover:bg-[#FFFFFF] hover:text-black hover:border-2 hover:border-black text-white ">Register</Button>
                </div>
            </div>
        </CardContent>
    </Card>
</div>
</template>
