<template>
    <input type="text" name="code" @input="$emit('inputCode', $event.target.value)"/>
</template>