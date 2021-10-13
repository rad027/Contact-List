<template>
    <div class="main">
        <header>
            <h1>{ Suiterus Technologies INC. }</h1>
            <h3>Are you interested here? Contact us at <a href="#">support@suiterus.com</a></h3>
        </header>
    </div>
</template>

<script>
export default {
    
    created(){
        this.$nextTick(()=>{
                     
        })
    }

}
</script>

<style scoped>
.main {
    background : linear-gradient(100deg, rgb(217, 215, 66) 0%, rgb(240, 158, 0) 100%); 
    width: 100vw; 
    min-height: 100vh;
    position: relative; 
    overflow: hidden;
    justify-content: center;
    align-content: center;
    align-items: center;
    display: flex;
}
header {
  margin: 0;
}
header h1, header h3 {
  text-align: center;
  font-family: 'Architects Daughter', cursive;
  color: #fff;
}
</style>