<script setup lang="ts">
import { cn } from "@/lib/utils";
import { cva } from "class-variance-authority";

const alertVariants = cva(
  "relative w-full rounded-lg border p-4 [&>svg~*]:pl-7 [&>svg+div]:translate-y-[-3px] [&>svg]:absolute [&>svg]:left-4 [&>svg]:top-4 [&>svg]:text-foreground",
  {
    variants: {
      variant: {
        default: "bg-background text-foreground",
        destructive:
          "border-destructive/50 text-destructive dark:border-destructive [&>svg]:text-destructive",
      },
    },
    defaultVariants: {
      variant: "default",
    },
  }
);

defineProps<{
  variant?: "default" | "destructive";
  extraClass?: string; // Changed "class" to "extraClass"
}>();
</script>

<template>
  <div :class="cn(alertVariants({ variant }), extraClass)" role="alert">
    <slot />
  </div>
</template>
