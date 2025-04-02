function otp() {
  return {
    timeLeft: 120, // 2 minutes countdown (in seconds)
    isActive: false, // Whether the countdown is active or not
    interval: null,
    reset() {
      this.timeLeft = 120; // 2 minutes countdown (in seconds)
      this.isActive = false; // Whether the countdown is active or not
      this.interval = null;
    },
    verifyOtp() {
      if (this.$wire.otp.length == 6) {
        this.$wire.verifyOtp();
      }
    },
    startCountdown() {
      this.isActive = true;
      this.timeLeft = 120;
      this.countdown();
    },
    countdown() {
      this.interval = setInterval(() => {
        if (this.timeLeft > 0) {
          this.timeLeft--;
        } else {
          clearInterval(this.interval);
          this.interval = null;
          this.isActive = false;
        }
      }, 1000);
    },
    get formattedTime() {
      const minutes = Math.floor(this.timeLeft / 60);
      const seconds = this.timeLeft % 60;
      return `${String(minutes).padStart(2, "0")}:${String(seconds).padStart(
        2,
        "0"
      )}`;
    }
  };
}

function imageUploader() {
  return {
    preview: this.$wire.entangle("preview"),
    imageTextField: this.$wire.entangle("thumbnail"),
    previewImage(event) {
      this.imageTextField = "";
      const file = event.target.files[0];
      if (file) {
        this.imageTextField = Math.floor(file.size / 1024);
      }

      if (file) {
        const reader = new FileReader();
        reader.onload = e => {
          this.preview = e.target.result;
        };
        reader.readAsDataURL(file);
      } else {
        this.preview = null;
      }
    }
  };
}

function toastr() {
  return {
    show: false,
    message: "",
    toggle(event) {
      this.show = true;
      this.type = event.type;
      this.message = event.message;

      setTimeout(() => {
        this.show = false;
      }, 2000);
    }
  };
}

function validatePriceFormat(input) {
  let formatted = input
    .replace(/[^0-9.]/g, "") // Allow only numbers and dot
    .replace(/(\..*)\./g, "$1") // Prevent multiple dots
    .replace(/^(\d*\.\d{2}).*$/, "$1"); // Limit to two decimals

  return formatted.includes(".") ? formatted : formatted + "."; // Ensure decimal presence
}

function flatpickrDate(tomorrow, allowedDates) {
  return {
    tomorrow: tomorrow,
    allowedDates: allowedDates,
    init() {
      const options = {
        dateFormat: "Y-m-d",
        inline: true,
        appendTo: this.$refs.calendarContainer
      };

      if (this.tomorrow) {
        const date = new Date(this.tomorrow);
        date.setDate(date.getDate());
        const formattedDate = date.toISOString().split("T")[0];
        options.minDate = this.tomorrow;
        options.defaultDate = formattedDate;
      }

      if (this.allowedDates) {
        options.enable = JSON.parse(this.allowedDates);
      }
      if (this.tomorrow || this.allowedDates) {
        flatpickr(this.$refs.dateInput, options);
      }
    },
    timeSlot(timeslot) {
      const [start, end] = timeslot.split(" - ");
      const formatTime = time => {
        const [hours, minutes, seconds] = time.split(":");
        return new Date(
          0,
          0,
          0,
          hours,
          minutes,
          seconds
        ).toLocaleTimeString("en-US", {
          hour: "numeric",
          minute: "2-digit",
          hour12: true
        });
      };

      return formatTime(start) + " - " + formatTime(end);
    },
    year(year) {
      return year ? year.split("-")[0] : "Select a date";
    },
    date(value) {
      if (value) {
        const date = new Date(value);
        return date.toLocaleDateString("en-US", {
          weekday: "short",
          month: "short",
          day: "2-digit"
        });
      } else {
        return "";
      }
    }
  };
}
