function otp() {
  return {
    timeLeft: 120, // 2 minutes countdown (in seconds)
    isActive: false, // Whether the countdown is active or not
    interval: null,
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
