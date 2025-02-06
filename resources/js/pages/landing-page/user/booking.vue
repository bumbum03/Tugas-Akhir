<template>
    <div class="container py-5">
      <div class="row justify-content-center">
        <div class="col-md-8">
          <div class="card">
            <div class="card-header bg-primary text-white">
              <h3 class="mb-0 mt-8">Villa Reservation Form</h3>
            </div>
            <div class="card-body">
              <!-- <form @submit.prevent="submitForm"> -->
                <div class="mb-3">
                  <label for="name" class="form-label">Full Name</label>
                  <input
                    type="text"
                    class="form-control"
                    id="name"
                    required
                  >
                </div>
  
                <div class="mb-3">
                  <label for="email" class="form-label">Email</label>
                  <input
                    type="email"
                    class="form-control"
                    id="email"
                    required
                  >
                </div>
  
                <div class="mb-3">
                  <label for="phone" class="form-label">Phone Number</label>
                  <input
                    type="tel"
                    class="form-control"
                    id="phone"
                    required
                  >
                </div>
  
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label for="checkIn" class="form-label">Check-in Date</label>
                    <input
                      type="date"
                      class="form-control"
                      id="checkIn"
                      required
                    >
                  </div>
  
                  <div class="col-md-6 mb-3">
                    <label for="checkOut" class="form-label">Check-out Date</label>
                    <input
                      type="date"
                      class="form-control"
                      id="checkOut"
                      required
                    >
                  </div>
                </div>
  
                <div class="mb-3">
                  <label for="guests" class="form-label">Number of Guests</label>
                  <input
                    type="number"
                    class="form-control"
                    id="guests"
                    min="1"
                    required
                  >
                </div>
  
                <div class="mb-3">
                  <label for="villaType" class="form-label">Villa Type</label>
                  <select
                    class="form-select"
                    id="villaType"
                    required
                  >
                    <option value="">Select Villa Type</option>
                    <option value="standard">Standard Villa</option>
                    <option value="deluxe">Deluxe Villa</option>
                    <option value="premium">Premium Villa</option>
                    <option value="royal">Royal Villa</option>
                  </select>
                </div>
  
                <div class="mb-3">
                  <label for="notes" class="form-label">Special Requests</label>
                  <textarea
                    class="form-control"
                    id="notes"
                    rows="3"
                  ></textarea>
                </div>
  
                <button type="submit" class="btn btn-primary">Submit Reservation</button>
              <!-- </form> -->
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>

<script setup lang = "ts">

</script>