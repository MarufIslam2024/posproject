<template>
     <div class="container-fluid">
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h2> Sales Report </h2>

                        <label class="form-label-mt-2"> Date From </label>
                       <input type="date" class="form-control" id="FormDate" >

                       <label class="form-label-mt-2"> To Date </label>
                       <input type="date" class="form-control" id="ToDate" >

                       <button  @click="SalesReport" class="btn mt-3 bg-primary">Creat PDF </button>

                    </div>
                </div>
            </div>
        </div>
     </div>

</template>

<script setup>
  const SalesReport = () => {
    let FormDate = document.getElementById('FormDate').value;
    let ToDate = document.getElementById('ToDate').value;
    if(FormDate.length === 0 || ToDate.length === 0){
        errorToast("Date Range Requred!")
    }else{
        window.open('/sales-report/'+FormDate+'/'+ToDate);
    }
  }

</script>