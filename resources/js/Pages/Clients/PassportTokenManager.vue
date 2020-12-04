<template>
    <div>
        <client-section />

        <jet-section-border />

        <personal-access-token-section />
    </div>
</template>

<script>
    import JetFormSection from '@/Jetstream/FormSection'
    import JetSectionBorder from '@/Jetstream/SectionBorder'
    import JetButton from '@/Jetstream/Button'
    import PersonalAccessTokenSection from "./PersonalAccessTokenSection";
    import ClientSection from "./ClientSection";

    export default {
        name: "PassportTokenManager",
        components: {
            ClientSection,
            PersonalAccessTokenSection,
            JetFormSection,
            JetButton,
            JetSectionBorder,
        },
    }
</script>

<style scoped>

</style>
