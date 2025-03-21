import { cn } from "@/lib/utils"
import { cva, type VariantProps } from "class-variance-authority"
import { defineComponent } from "vue"

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
)

export const Alert = defineComponent({
  props: {
    variant: {
      type: String,
      default: "default"
    },
    class: {
      type: String,
      default: ""
    }
  },
  setup(props, { slots }) {
    return () => (
      <div
        class={cn(alertVariants({ variant: props.variant }), props.class)}
        role="alert"
      >
        {slots.default?.()}
      </div>
    )
  }
})

export const AlertTitle = defineComponent({
  setup(_, { slots }) {
    return () => (
      <h5 class="mb-1 font-medium leading-none tracking-tight">
        {slots.default?.()}
      </h5>
    )
  }
})

export const AlertDescription = defineComponent({
  setup(_, { slots }) {
    return () => (
      <div class="text-sm [&_p]:leading-relaxed">
        {slots.default?.()}
      </div>
    )
  }
}) 