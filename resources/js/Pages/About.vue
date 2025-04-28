<template>
  <div class="bg-gray-50 py-12 sm:py-16 lg:py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center">
        <h2 class="text-3xl font-extrabold text-gray-900">About Us</h2>
        <p class="mt-4 text-lg text-gray-600">Learn more about our team and mission</p>
      </div>

      <!-- Team Section -->
      <section class="mt-16">
        <div class="flex justify-between items-center">
          <h3 class="text-2xl font-semibold text-gray-800">Our Team</h3>
          <button
            @click="showMemberForm = true"
            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors"
          >
            Add Team Member
          </button>
        </div>

        <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
          <div v-for="(member, index) in teamMembers" :key="index" class="bg-white p-6 rounded-lg shadow">
            <img :src="member.image" class="w-32 h-32 mx-auto rounded-full" :alt="member.name">
            <h3 class="mt-4 text-xl font-semibold text-gray-900 text-center">{{ member.name }}</h3>
            <p class="mt-1 text-gray-600 text-center">{{ member.role }}</p>
            <div class="mt-4 flex justify-center space-x-2">
              <button @click="editMember(index)" class="text-blue-600 hover:text-blue-800">Edit</button>
              <button @click="deleteMember(index)" class="text-red-600 hover:text-red-800">Delete</button>
            </div>
          </div>
        </div>
      </section>

      <!-- Team Members Table -->
      <section class="mt-16">
        <h3 class="text-2xl font-semibold text-gray-800 mb-4">Team Members Table</h3>
        <div class="overflow-x-auto shadow-md rounded-lg">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-100">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Photo</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="(member, index) in teamMembers" :key="'table-'+index">
                <td class="px-6 py-4 whitespace-nowrap">
                  <img :src="member.image" class="h-10 w-10 rounded-full" :alt="member.name">
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ member.name }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ member.role }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  <button
                    @click="editMember(index)"
                    class="text-blue-600 hover:text-blue-900 mr-3"
                  >
                    Edit
                  </button>
                  <button
                    @click="deleteMember(index)"
                    class="text-red-600 hover:text-red-900"
                  >
                    Delete
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>

      <!-- Mission Statement -->
      <section class="mt-16 text-center">
        <h3 class="text-2xl font-semibold text-gray-800">Our Mission</h3>
        <p class="mt-4 text-lg text-gray-600">
          Our mission is to provide high-quality, user-friendly solutions that empower businesses to succeed in the digital world.
        </p>
      </section>

      <!-- Contact Section -->
      <section class="mt-16 text-center">
        <h3 class="text-2xl font-semibold text-gray-800">Contact Us</h3>
        <p class="mt-4 text-lg text-gray-600">Have any questions? Get in touch with us below:</p>
        <div class="mt-8">
          <a href="mailto:info@company.com" class="text-blue-500 hover:text-blue-700 text-xl">info@company.com</a>
        </div>
      </section>

      <!-- Add/Edit Team Member Modal -->
      <div v-if="showMemberForm" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
          <h3 class="text-xl font-semibold mb-4">{{ editingIndex !== null ? 'Edit Member' : 'Add New Member' }}</h3>

          <form @submit.prevent="saveMember">
            <div class="mb-4">
              <label class="block text-gray-700 text-sm font-bold mb-2">Name</label>
              <input
                v-model="newMember.name"
                type="text"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                required
              >
            </div>

            <div class="mb-4">
              <label class="block text-gray-700 text-sm font-bold mb-2">Role</label>
              <input
                v-model="newMember.role"
                type="text"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                required
              >
            </div>

            <div class="mb-6">
              <label class="block text-gray-700 text-sm font-bold mb-2">Image URL</label>
              <input
                v-model="newMember.image"
                type="url"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                placeholder="https://example.com/image.jpg"
                required
              >
            </div>

            <div class="flex justify-end space-x-3">
              <button
                type="button"
                @click="cancelEdit"
                class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
              >
                Cancel
              </button>
              <button
                type="submit"
                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
              >
                {{ editingIndex !== null ? 'Update' : 'Save' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

// Team data with reactive state
const teamMembers = ref([
  {
    name: 'John Doe',
    role: 'CEO & Founder',
    image: 'https://via.placeholder.com/150'
  },
  {
    name: 'Jane Smith',
    role: 'Chief Technology Officer',
    image: 'https://via.placeholder.com/150'
  },
  {
    name: 'Sara Lee',
    role: 'Marketing Director',
    image: 'https://via.placeholder.com/150'
  }
]);

// Form state
const showMemberForm = ref(false);
const editingIndex = ref(null);
const newMember = ref({
  name: '',
  role: '',
  image: ''
});

// Methods
const deleteMember = (index) => {
  teamMembers.value.splice(index, 1);
};

const editMember = (index) => {
  editingIndex.value = index;
  newMember.value = { ...teamMembers.value[index] };
  showMemberForm.value = true;
};

const saveMember = () => {
  if (editingIndex.value !== null) {
    // Update existing member
    teamMembers.value[editingIndex.value] = { ...newMember.value };
  } else {
    // Add new member
    teamMembers.value.push({ ...newMember.value });
  }
  cancelEdit();
};

const cancelEdit = () => {
  showMemberForm.value = false;
  editingIndex.value = null;
  newMember.value = { name: '', role: '', image: '' };
};
</script>

<style scoped>
/* Custom styles can be added here */
</style>
