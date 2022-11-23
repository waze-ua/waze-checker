import { Module } from '@nestjs/common';
import { TypeOrmModule } from '@nestjs/typeorm';
import { ConnectionController } from './connection.controller';
import { ConnectionEntity } from './connection.entity';
import { ConnectionService } from './connection.service';

@Module({
  imports: [TypeOrmModule.forFeature([ConnectionEntity])],
  controllers: [ConnectionController],
  providers: [ConnectionService],
})
export class ConnectionModule {}
