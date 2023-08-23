# Zoom meeting customer support extension(graphql supported)
This module is a customer support module which uses the zoom api.

## Flow
* Customer support agents can host meeting
* Each customer support agent can be assigned in a specific category like mobiles / home appliances / laptops etc.. so that he can give suppport related to that category
* When a customer is click on a link for the online virtual support, he will be asked to select a category
* If the custome select a category and click on connect, the system will check if customer support agent in the specific category have hosted any meeting and he is available or not
* if he is available then a zoom meeting link will be shared to the customer to join it. if the cusatomer have the meeting app it will be automatically joined
