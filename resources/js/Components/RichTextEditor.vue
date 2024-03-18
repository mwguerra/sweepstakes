
<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue';
import { useEditor, EditorContent } from '@tiptap/vue-3';
import StarterKit from '@tiptap/starter-kit';
import Image from '@tiptap/extension-image';

const props = defineProps({
  modelValue: String,
});

const emits = defineEmits(['update:modelValue']);

const editor = useEditor({
    extensions: [
      StarterKit,
      Image.configure({
        inline: true, // Use inline images
        HTMLAttributes: {
          class: 'editor-image',
        },
      }),
    ],
    content: props.modelValue,
    onUpdate: ({ editor }) => {
      // Safely call getHTML
      if (editor && editor.getHTML) {
        const html = editor.getHTML();
        emits('update:modelValue', html);
      }
    },
    editorProps: {
      handlePaste: (view, event, slice) => {
        if (event.clipboardData) {
          const items = event.clipboardData.items;
          for (let i = 0; i < items.length; i++) {
            if (items[i].type.indexOf('image') === 0) {
              const file = items[i].getAsFile();
              const reader = new FileReader();
              reader.onload = (readerEvent) => {
                const base64 = readerEvent.target.result;
                const transaction = view.state.tr;
                const node = view.state.schema.nodes.image.create({
                  src: base64,
                });
                transaction.replaceSelectionWith(node);
                view.dispatch(transaction);
              };
              reader.readAsDataURL(file);
              return true; // Prevent the default handling of the paste
            }
          }
        }
        return false; // If not handling paste, let Tiptap handle it
      },
    },
  });

// React to external updates of modelValue
watch(props, (newProps) => {
  if (editor.value && newProps.modelValue !== editor.value.getHTML()) {
    editor.value.commands.setContent(newProps.modelValue);
  }
}, { deep: true });
</script>

<template>
  <editor-content :editor="editor" />
</template>
