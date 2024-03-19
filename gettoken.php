 <input type="text" class="form-control" name="receivingaddress" id="receivingaddress" value="<?php echo $campaign['receivingaddress']; ?>" required>
							<div id="validationMessage"></div> 

<script src="https://unpkg.com/@solana/web3.js@latest/lib/index.iife.min.js"></script>
<script>
$(document).ready(function() {
 const customRpcUrl = 'https://mainnet.helius-rpc.com/?api-key=<your key>';
 
  const mintAddress = new solanaWeb3.PublicKey("kinXdEcpDQeHPEuQnqmUgtYykqKGVFq6CeVX5iAHJq6");
    const solana = new solanaWeb3.Connection(customRpcUrl);
    const validationMessageElement = document.getElementById('validationMessage'); // Validation message element

  $('#receivingaddress').on('input', function() {
        const inputAddress = $(this).val().trim();
        validationMessageElement.innerHTML = ''; // Clear previous messages
        if (inputAddress) {
            updateKinAddress(inputAddress, mintAddress, solana);
        }
    });

 async function updateKinAddress(solanaAddress, mintAddress, solanaConnection) {
        try {
            const userPublicKey = new solanaWeb3.PublicKey(solanaAddress);
            const accounts = await solanaConnection.getTokenAccountsByOwner(userPublicKey, { mint: mintAddress });

            if (accounts.value.length > 0) {
                const kinAddress = accounts.value[0].pubkey.toString();
				console.log('kin address'+kinAddress);
                $('#receivingaddress').val(kinAddress); // Update the input with the Kin token address
                validationMessageElement.innerHTML = '<span style="color:#84bc0e;">Kin token address found. Derived from Solana address.</span>';
                validateKinAddress($('#receivingaddress').get(0)); // Validate and update UI accordingly
            } else {
               // validationMessageElement.innerHTML = '<span style="color:#b61414;">No associated Kin token account found for this Solana address.</span>';
            }
        } catch (error) {
            console.error("Error fetching token account:", error);
            validationMessageElement.innerHTML = '<span style="color:#b61414;">processing solana address</span>';
        }
    }
	async function isKinTokenAddress(address) {
		try {
			const publicKey = new solanaWeb3.PublicKey(address);
			// If the address can be parsed as a Solana public key, it's a valid format
			console.log("Valid KIN token address format:", publicKey.toString());
			return true; // The address is a valid Solana public key format
		} catch (error) {
			console.error("Invalid KIN token address format:", error);
			return false; // The address is not a valid Solana public key format
		}
	}


    async function validateKinAddress(inputElement) {
		const realkin = inputElement.value;
		
        if (inputElement.value.length === 0) {
            validationMessageElement.innerHTML = '';
            inputElement.classList.remove('valid-input', 'invalid-input');
            return;
        }
        const isValid = await isKinTokenAddress(inputElement.value);
        if (isValid) {
            inputElement.classList.add('valid-input');
            inputElement.classList.remove('invalid-input');
            validationMessageElement.innerHTML = '<span style="color:#84bc0e;"><b>KIN token address found</b></span>';
			 $('#receivingaddress').val(realkin); // Update the input with the Kin token address
        } else {
            inputElement.classList.add('invalid-input');
            inputElement.classList.remove('valid-input');
            validationMessageElement.innerHTML = '<span style="color:#b61414;">Invalid KIN token address. Double check your deposit address in code wallet</span>';
        }
    }

   $('#receivingaddress').on('input change blur', async function(e) {
        await validateKinAddress(e.target);
    });
});
</script>

