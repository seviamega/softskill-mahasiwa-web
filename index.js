firebase.auth().onAuthStateChanged(function(user) {
    if (user) {
      document.getElementById("user_div").style.display = "block";
      document.getElementById("login_div").style.display = "none";
      var user = firebase.auth().currentUser;
  
      if(user != null){
      var email_id = user.email;
      document.getElementById("user_para").innerHTML = "Welcome User : " + email_id;
      }
  
    } else {
      document.getElementById("user_div").style.display = "none";
      document.getElementById("login_div").style.display = "block";
  
    }
  });
  
  function login(){
    var userEmail = document.getElementById("email_field").value;
    var userPass = document.getElementById("password_field").value;
    firebase.auth().signInWithEmailAndPassword(userEmail, userPass).catch(function(error) {
     
      var errorCode = error.code;
      var errorMessage = error.message;
      window.alert("Error : " + errorMessage);
  
    });
  
  }
  
  function logout(){
    firebase.auth().signOut();
  }

  // CREATE
  var db = firebase.database();
  var reviewForm = document.getElementById('reviewForm');
  var kesalahan  = document.getElementById('kesalahan');
  var poin_kesalahan   = document.getElementById('poin_kesalahan');
  var ket    = document.getElementById('ket');
  var hiddenId   = document.getElementById('hiddenId');
  
  reviewForm.addEventListener('submit', (e) => {
    alert("Sukses");
    e.preventDefault();
  
    if (!kesalahan.value || !poin_kesalahan.value || !ket.value) return null
  
    var id = hiddenId.value || firebase.database().ref('DaftarKesalahan').push().key;
  
    db.ref('DaftarKesalahan/' + id).set({
      id_kesalahan:id,
      kesalahan: kesalahan.value,
      poin_kesalahan: poin_kesalahan.value,
      status:ket.value
    });
  
    kesalahan.value = '';
    poin_kesalahan.value  = '';
    ket.value = ' ';
    hiddenId.value = '';
  });
  
  // READ 
  
  var DaftarKesalahan = document.getElementById('DaftarKesalahan');
  var reviewsRef = db.ref('/DaftarKesalahan');
  
  reviewsRef.on('child_added', (data) => {
    var li = document.createElement('li')
    li.id = data.key;
    li.innerHTML = reviewTemplate(data.val())
    reviews.appendChild(li);
  });
  
  reviewsRef.on('child_changed', (data) => {
    var reviewNode = document.getElementById(data.key);
    reviewNode.innerHTML = reviewTemplate(data.val());
  });
  
  reviewsRef.on('child_removed', (data) => {
    var reviewNode = document.getElementById(data.key);
    reviewNode.parentNode.removeChild(reviewNode);
  });
  
  reviews.addEventListener('click', (e) => {
    var reviewNode = e.target.parentNode
  
    // UPDATE 
    if (e.target.classList.contains('edit')) {

      kesalahan.value = reviewNode.querySelector('.kesalahan').innerText;
      poin_kesalahan.value  = reviewNode.querySelector('.poin_kesalahan').innerText;
      ket.value  = reviewNode.querySelector('.status').innerText;
      hiddenId.value = reviewNode.id;
    }
  
   
    // DELETE
    if (e.target.classList.contains('delete')) {
      var answer = confirm("Yakin menghapus kesalahan?");
      var id = reviewNode.id;
      if (answer) {
      
      db.ref('DaftarKesalahan/' + id).remove();
    }
  }
  });
  
  function reviewTemplate({kesalahan, poin_kesalahan, status}) {
    return `
      <div class='kesalahan'>${kesalahan}</div>
      <div class='poin_kesalahan'>${poin_kesalahan}</div>
      <div class='status'>${status}</div>
      <button class='delete'>Hapus</button>
      <button class='edit'>Edit</button>
    `
  };
  $('.toast').toast('show');