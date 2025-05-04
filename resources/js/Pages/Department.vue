<template>
    <v-container>
        <v-card class="pa-5">
            <v-card-title>
                <h1>Department Management</h1>
            </v-card-title>
            <v-card-text>
                <v-btn color="primary" @click="addDepartment" class="mb-4">
                    Add Department
                </v-btn>
                <v-list>
                    <v-list-item
                        v-for="department in departments"
                        :key="department.id"
                        class="d-flex justify-space-between align-center"
                    >
                        <v-list-item-content>
                            <v-list-item-title>{{ department.name }}</v-list-item-title>
                        </v-list-item-content>
                        <v-list-item-action>
                            <v-btn color="warning" small @click="editDepartment(department.id)">
                                Edit
                            </v-btn>
                            <v-btn color="error" small @click="deleteDepartment(department.id)">
                                Delete
                            </v-btn>
                        </v-list-item-action>
                    </v-list-item>
                </v-list>
            </v-card-text>
        </v-card>
    </v-container>
</template>

<script>
export default {
    data() {
        return {
            departments: [
                { id: 1, name: "HR" },
                { id: 2, name: "Finance" },
                { id: 3, name: "IT" },
            ],
        };
    },
    methods: {
        addDepartment() {
            // Logic to add a new department
            alert("Add Department clicked");
        },
        editDepartment(id) {
            // Logic to edit a department
            alert(`Edit Department with ID: ${id}`);
        },
        deleteDepartment(id) {
            // Logic to delete a department
            this.departments = this.departments.filter((dept) => dept.id !== id);
        },
    },
};
</script>

<style scoped>
.v-card {
    max-width: 600px;
    margin: auto;
}
</style>
