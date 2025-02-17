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
    preview: "",
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
        this.preview = "";
      }
    }
  };
}

function toastr() {
  return {
    show: false,
    type: "",
    message: "",
    toggle(event) {
      this.show = true;
      this.type = event.type;
      this.message = event.message;

      setTimeout(() => {
        this.show = false;
      }, 4000);
    }
  };
}

function flatpickrDate(tomorrow) {
  return {
    tomorrow: tomorrow,
    init() {
      
      const options = {
        dateFormat: "Y-m-d",
        inline: true,
        appendTo: this.$refs.calendarContainer
      };
      
      if(this.tomorrow){
        const date = new Date(this.tomorrow);
        date.setDate(date.getDate());
        const formattedDate = date.toISOString().split("T")[0];
        options.minDate = this.tomorrow;
        options.defaultDate = formattedDate;
      }
      console.log(this.$refs.dateInput);

      flatpickr(this.$refs.dateInput, options);
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
      return year.split("-")[0];
    },
    date(value) {
      const date = new Date(value);
      return date.toLocaleDateString("en-US", {
        weekday: "short",
        month: "short",
        day: "2-digit"
      });
    }
  };
}
