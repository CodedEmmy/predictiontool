# CrowdWise Prediction DApp - README

Welcome to the **CrowdWise Prediction DApp** project repository! This decentralized application (DApp) leverages blockchain technology to implement a prediction/forecasting platform on the Solana network.

## Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Setup](#setup)
  - [Prerequisites](#prerequisites)
  - [Installation](#installation)
- [Usage](#usage)
- [Tech Stack](#tech-stack)
- [Future Updates](#future-updates)
- [License](#license)

## Overview

Predictions are a part of many activities in various domains ranging from our personal lives to business processes. However, it becomes quite difficult to make these decisions if there is no historical data to rely on.
A suitable technique for handling such situations is to crowd-source opinions from the public/other people. This is called a crowd prediction system. A crowd prediction system allows us to make forecasts/predictions based on the aggregate of the choices from a group (crowd) of forecasters.
**CrowdWise** is a tool that utilizes the crowd prediction system to help users make decisions based on the "wisdom of the crowd". **CrowdWise ** provides a user-friendly interface for users to participate in the prediction polls. The platform provides an option for incentivised polls that reward users for participating in the system. Incentives are handled with the integration of the Solana blockchain which allows the streamlining of payments. The Solana blockchain is also used for defining user accounts and verification of certain user actions such as voting, poll creation, etc.

## Features

- Web3 Login
- Phantom Wallet Integration: Connect your Solana wallet (currently supports Phantom) to participate on the platform.
- Each user account is defined and represented by their Solana wallet.
- Incentivised polls using crypto tokens. (Users earn an equal share of the reward pool if they participate in an incentivised prediction poll).
- User activity verification via crypto wallet signatures. This reduces the possibility of spam activity.
- Claiming Rewards: Users can easily claim their accumulated poll rewards from the dApp to their on-chain Solana wallets.
- Creators of prediction polls can chose to make the poll results public or private. When public, all participants can see the poll results. If private,only the poll creator can view the results.
- Users can create any number of prediction polls and vote on any available poll.
- Creating a prediction poll requires a small creation fee. This primarily aims to reduce spam polls. Users can pay the creator fee from their earned poll rewards.

## Setup

Follow these steps to set up the DApp locally. (Similar steps are applicable for setting up on hosted servers)

### Prerequisites

1. Web Server with support for PHP.
2. MySQL database
A server bundle such as [XAMPP](https://www.apachefriends.org/) or [WAMPServer](https://www.wampserver.com/en/) can be used. These server bundles contain and provide easy access to the above servers and incorporate phpMyAdmin which make server management easy.
3. Phantom Solana Wallet

### Installation

1. Clone or download the 'dist' folder from the repository:

2. Copy the 'crowdwise' folder from the 'dist' folder to the your web server's http/web folder.

3. Setup the database by importing the 'crowdtool.sql' file into your mySql database. You can accomplish this easily by using a tool like phpMyAdmin.

4. Configure the database settings: if you are not using the default database settings then go to the crowdwise folder in your web server, open the dbconfig.php file in an editor and change the database connecton paramaters to match your setup. 

5. Configure the dApp settings: Go to the crowdwise folder in your web server, open the appconstants.php file in an editor and configure the various settings such as RPC endpoint, platform fees, dApp wallet address, etc. 

6. Install and fund a phantom wallet browser extension


## Usage

1. Open your web browser and navigate to `http://localhost/crowdwise/` to access the DApp. (If you are not running a local server, then replace localhost with your server URL)

2. Connect your Phantom Solana wallet to the DApp. If this is your first time connecting a particular Solana account, a new user account linked to the Solana account will automatically be registered on the platform.


`Note: the default network is Solana Devnet. Set your Phantom wallet network to match this or configure your preferred network in the appconstants.php file.`


3. Explore the various features of the dApp; create prediction polls, vote on available polls, earn and claim rewards, and view and monitor your activities.


## Tech Stack

The DApp frontend is built using web technologies including HTML, Css. Other technologies used include:

- **PHP**: Powers the DApp's business logic.
- **Solana Web3.js**: The Solana JavaScript API for interacting with the Solana blockchain.
- **Phantom**: A popular Solana wallet browser extension for secure transactions.


## Future Updates

This DApp is the first module in an ecosystem that will be built around the concept of crowd-based initiatives such as crowd-funding/donations, Social tipping, product promotion drive (mass shilling) and other similar ideas.

For this module, future features to include
1. Minting of public poll results as NFTs on the blockchain. This will make it accessible to everyone and provide some sort of historical/reference dataset for similar topics.
2. Support for multiple Solana wallets.
3. Expand incentive payments to support other SPL tokens, in addition to the native SOL token.
4. Addition of Fiat on/off-ramp for users.
5. Include an Admin UI for dynamic configuration of dApp settings.

## License

This project is licensed under the [Apache 2.0 License](LICENSE).

---
